<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Instagram\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;
use Thelia\Model\ConfigQuery;
use Instagram\Form\ConfigForm;
use Instagram\Instagram;
use Instagram\Exception\CredentialValidationException;


/**
 * Class ConfigController
 * @package Instagram\Controller
 * @author Emmanuel Nurit <enurit@openstudio.fr>
 */ 
 
class InstagramToken extends BaseFrontController
{
    public function saveCode(){	

		if(isset($_REQUEST['access_token'])){ ConfigQuery::write('instagram_access_token', $_REQUEST['token'], 1, 1);
		  echo 'new token created.';
		}else{
		$ch = curl_init('https://api.instagram.com/oauth/access_token');
		$encoded = '';
		ConfigQuery::write('instagram_code', $_REQUEST['code'], 1, 1);
		$_POST['client_id']=ConfigQuery::read('instagram_client_id');
		$_POST['client_secret']=ConfigQuery::read('instagram_client_secret');
		$_POST['code']=ConfigQuery::read('instagram_code');
		$_POST['grant_type']='authorization_code';
		$_POST['redirect_uri']=URL::getInstance()->absoluteUrl('/module/Instagram/access');

		// include GET as well as POST variables; your needs may vary.
	/*	foreach($_GET as $name => $value) {
		  $encoded .= urlencode($name).'='.urlencode($value).'&';
		}*/
		foreach($_POST as $name => $value) {
		  $encoded .= urlencode($name).'='.urlencode($value).'&';
		}
		// chop off last ampersand
		$encoded = substr($encoded, 0, strlen($encoded)-1);
		
//		echo $encoded .= '&redirect_uri=' . ConfigQuery::read('url_site') . '/module/Instagram/token';
//		echo $encoded;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,  $encoded);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		$retour = curl_exec($ch);
		$jsonretour = json_decode($retour);
		curl_close($ch);
			if(isset($jsonretour->access_token)){ 
				ConfigQuery::write('instagram_access_token', $jsonretour->access_token, 1, 1);
				
				$ch = curl_init('https://graph.instagram.com/access_token?grant_type=ig_exchange_token&client_secret='. ConfigQuery::read('instagram_client_secret') .'&access_token=' . $jsonretour->access_token);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_POST, 0);
				$retourLongToken = curl_exec($ch);
				$jsonRetourLongToken = json_decode($retourLongToken);
				curl_close($ch);

	//			print_r($jsonRetourLongToken);
				$quand = new \dateTime('+ '. $jsonRetourLongToken->expires_in .'seconds');
				ConfigQuery::write('instagram_access_token', $jsonRetourLongToken->access_token, 1, 1);
				ConfigQuery::write('instagram_token_expire', $quand->format('Y-m-d'), 1, 1);
			//	echo 'new token created.';
				
			}
		}
		return $response = RedirectResponse::create(URL::getInstance()->absoluteUrl('/admin/module/Instagram'));
    }
	
	public function refreshToken(){
		$ch = curl_init('https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . ConfigQuery::read('instagram_access_token'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 0);
		$retourLongToken = curl_exec($ch);
		$jsonRetourLongToken = json_decode($retourLongToken);
		curl_close($ch);

		// print_r($jsonRetourLongToken);
		$quand = new \dateTime('+ '. $jsonRetourLongToken->expires_in .'seconds');
		ConfigQuery::write('instagram_access_token', $jsonRetourLongToken->access_token, 1, 1);
		ConfigQuery::write('instagram_token_expire', $quand->format('Y-m-d'), 1, 1);
		echo 'old token refresh.';
		exit;
	}
	public function saveToken(){
		if(isset($_REQUEST['token'])) ConfigQuery::write('instagram_access_token', $_REQUEST['token'], 1, 1);
		echo 'new token created.';
		exit;
    }
}
