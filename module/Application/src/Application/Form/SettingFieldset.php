<?php
namespace Application\Form;

use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

class SettingFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    public function init()
    {
        parent::__construct('setting');

        $vocabulary = $this->getVocabulary();

        $this->add(array(
            'name' => 'aboutProfileImage',
            'type' => 'file',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_ABOUT_PROFILE_IMAGE"])
            ),
        ));

        $this->add(array(
            'name' => 'homeImage',
            'type' => 'file',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_HOME_IMAGE"])
            ),
        ));

        $this->add(array(
            'name' => 'aboutImage',
            'type' => 'file',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_ABOUT_IMAGE"])
            ),
        ));

        $this->add(array(
            'name' => 'galleriesImage',
            'type' => 'file',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_GALLERIES_IMAGE"])
            ),
        ));


        $this->add(array(
            'name' => 'servicesImage',
            'type' => 'file',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_SERVICES_IMAGE"])
            ),
        ));

        $this->add(array(
            'name' => 'contactImage',
            'type' => 'file',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_CONTACT_IMAGE"])
            ),
        ));

        $this->add(array(
            'name' => 'aboutImage',
            'type' => 'file',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_ABOUT_IMAGE"])
            ),
        ));

//        $this->add(array(
//            'name' => 'aboutFileOne',
//            'type' => 'file',
//            'options' => array(
//                'label' => $this->getTranslator()->translate($vocabulary["LABEL_ABOUT_FILE_ONE"])
//            ),
//            'attributes' => array(
//                'placeholder' => $this->getTranslator()->translate($vocabulary["PLACEHOLDER_SELECT_IMAGE"])
//            ),
//        ));
//
//        $this->add(array(
//            'name' => 'aboutFileTwo',
//            'type' => 'file',
//            'options' => array(
//                'label' => $this->getTranslator()->translate($vocabulary["LABEL_ABOUT_FILE_TWO"])
//            ),
//            'attributes' => array(
//                'placeholder' => $this->getTranslator()->translate($vocabulary["PLACEHOLDER_SELECT_IMAGE"])
//            ),
//        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'aboutProfileImage' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'Application\Validator\Image',
                        'options' => array(
                            'maxSize' => '40960',
                        )
                    ),
                ),
            ),
            'homeImage' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'Application\Validator\Image',
                        'options' => array(
                            'maxSize' => '40960',
                        )
                    ),
                ),
            ),
            'aboutImage' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'Application\Validator\Image',
                        'options' => array(
                            'maxSize' => '40960',
                        )
                    ),
                ),
            ),
            'galleriesImage' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'Application\Validator\Image',
                        'options' => array(
                            'maxSize' => '40960',
                        )
                    ),
                ),
            ),
            'servicesImage' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'Application\Validator\Image',
                        'options' => array(
                            'maxSize' => '40960',
                        )
                    ),
                ),
            ),
            'contactImage' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'Application\Validator\Image',
                        'options' => array(
                            'maxSize' => '40960',
                        )
                    ),
                ),
            ),
        );
    }

}