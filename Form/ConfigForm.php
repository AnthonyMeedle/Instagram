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

namespace Instagram\Form;

use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\ConfigQuery;
use Instagram\Instagram;

/**
 * Class ConfigForm
 * @package Instagram\Form
 * @author Emmanuel Nurit <enurit@openstudio.fr>
 */
class ConfigForm extends BaseForm
{

    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        $translator = Translator::getInstance();

        $this->formBuilder->add(
            'access_token',
            'text',
            [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => $translator->trans('access token', [], 'instagram'),
                'data' => ConfigQuery::read('instagram_access_token')
            ]
        )
        ->add(
            'username',
            'text',
            [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => $translator->trans('username', [], 'instagram'),
                'data' => ConfigQuery::read('instagram_username')
            ]
        )
		->add(
            'photos_quantity',
            'text',
            [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => $translator->trans('photos quantity', [], 'instagram'),
                'data' => ConfigQuery::read('instagram_photos_quantity')
            ]
        )

        ;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return 'instagram_config';
    }
}
