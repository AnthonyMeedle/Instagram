<?php
namespace Instagram\Loop;

use Instagram\Instagram;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Model\ConfigQuery;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Element\SearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Type\BooleanOrBothType;
use Thelia\Core\Event\Image\ImageEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Type\TypeCollection;
use Thelia\Type\EnumType;

class instagramLoop extends BaseLoop implements PropelSearchLoopInterface{
	protected $_instagram;
    protected function getArgDefinitions(){
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createIntTypeArgument('debug'),
            Argument::createIntTypeArgument('limit'),
            Argument::createBooleanOrBothTypeArgument('visible'),
		 	Argument::createIntTypeArgument('width'),
            Argument::createIntTypeArgument('height'),
            Argument::createIntTypeArgument('rotation', 0),
            Argument::createAnyTypeArgument('background_color'),
            Argument::createIntTypeArgument('quality'),
            new Argument(
                'resize_mode',
                new TypeCollection(
                    new EnumType(array('crop', 'borders', 'none'))
                ),
                'none'
            ),
            Argument::createAnyTypeArgument('effects'),
            Argument::createBooleanTypeArgument('allow_zoom', false)
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria(){
		$search = ConfigQuery::create()->filterByName('instagram_access_token');
        return $search;
    }


    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult){

        $access_token = ConfigQuery::read('instagram_access_token');
		$url = "https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,permalink,thumbnail_url&access_token=". $access_token;
        $shots = @file_get_contents($url); 
        $query = json_decode($shots);
		$retour='';
		if($this->getDebug()){

				// create curl resource
				$ch = curl_init();
				// set url
				curl_setopt($ch, CURLOPT_URL, $url);
				//return the transfer as a string
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				// $output contains the output string
				$output = curl_exec($ch);
				// close curl resource to free up system resources
				curl_close($ch);      
			$outputObj = json_decode($output);
			
			$retour .= $url . '<br>' . $shots . '<br>' . $output;
			
			if($outputObj->error->message) $retour = '<strong style="color:red">'. $outputObj->error->message .'</strong><br>' . $retour ; 
			
			
		/*	 echo '<pre>';
		
			print_r($shots);
			echo '</pre>'; */
			
			
			$loopResultRow = new LoopResultRow();
			$loopResultRow
				->set('ID', '')
				->set('CAPTION', '')
				->set('ALT', '')
				->set('MEDIA_TYPE', '')
				->set('MEDIA_URL', '')
				->set('URL', '')
				->set('IMAGE_URL', '')
				->set('IMAGE_ORIGINE', '')
				->set('RETOUR', $retour)
			;
			$loopResult->addRow($loopResultRow);
			
		} 
		
        $compteur=0;
        $limit = $this->getLimit();
        if(!$limit)$limit=10;
        if($query){
		foreach($query->data as $info){
            $compteur++;
            if($compteur > $limit) break;
			
			if($info->media_type == 'VIDEO'){
                $image_origine = $info->thumbnail_url;
            }else{
                $image_origine = $info->media_url;
            }
            
            $exif = exif_read_data($image_origine, 0, true);
            $extension = '.jpg';
            switch($exif['FILE']['MimeType']){
                case 'image/jpeg' : $extension = '.jpg'; break;
                case 'image/png' : $extension = '.png'; break;
                default: $extension = '.jpg'; break;
            }
            $newfile = 'instagram_' . $info->id . $extension;
            $dossier = Instagram::getUploadDir();
            $retour = copy($image_origine, $dossier . DS . $newfile);
            $event = new ImageEvent();
            $event->setSourceFilepath($dossier . DS . $newfile)->setCacheSubdirectory('instagram');
            switch ($this->getResizeMode()) {
					case 'crop':
						$resize_mode = \Thelia\Action\Image::EXACT_RATIO_WITH_CROP;
					break;
					case 'borders':
						$resize_mode = \Thelia\Action\Image::EXACT_RATIO_WITH_BORDERS;
					break;
					case 'none':
					default:
						$resize_mode = \Thelia\Action\Image::KEEP_IMAGE_RATIO;
				}

				// Prepare tranformations
				$width = $this->getWidth();
				$height = $this->getHeight();
				$rotation = $this->getRotation();
				$background_color = $this->getBackgroundColor();
				$quality = $this->getQuality();
				$effects = $this->getEffects();

				if (!is_null($width)) {
					$event->setWidth($width);
				}
				if (!is_null($height)) {
					$event->setHeight($height);
				}
				$event->setResizeMode($resize_mode);
				if (!is_null($rotation)) {
					$event->setRotation($rotation);
				}
				if (!is_null($background_color)) {
					$event->setBackgroundColor($background_color);
				}
				if (!is_null($quality)) {
					$event->setQuality($quality);
				}
				if (!is_null($effects)) {
					$event->setEffects($effects);
				}

            $event->setAllowZoom($this->getAllowZoom());
            $this->dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $event);
            $image_url = $event->getFileUrl();
            
			$loopResultRow = new LoopResultRow();
			$loopResultRow
				->set('ID', $info->id)
				->set('CAPTION', $info->caption)
				->set('ALT', str_replace('"', '', strip_tags( $info->caption) ) )
				->set('MEDIA_TYPE', $info->media_type)
				->set('MEDIA_URL', $info->media_url)
				->set('URL', $info->permalink)
				->set('IMAGE_URL', $image_url)
				->set('IMAGE_ORIGINE', $image_origine)
				->set('RETOUR', $retour)

			;
			$loopResult->addRow($loopResultRow);
		}	
        }

        return $loopResult;
    }
	
    public function getUserMedia($limit = 5, $page = 1)
    {
        $data = $this->_instagram->getUserMedia('self', $limit);
        if($page > 1){
            for($i=$page; $i>1; $i--) {
                $data = $this->_instagram->pagination($data, $limit);
            }
        }
        return $data;
    }

}