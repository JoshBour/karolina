<?php
namespace Application\Form;

use Zend\Form\Form;

class SettingForm extends Form
{
    public function __construct()
    {
        parent::__construct('settingForm');

        $this->setAttributes(array(
            'method' => 'post',
            'class' => 'standardForm'
        ));

//        $this->add(array(
//            'name' => 'security',
//            'type' => 'Zend\Form\Element\Csrf'
//        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit'
        ));

        $this->setValidationGroup(array(
//            'security',
            'setting' => array(
                'aboutProfileImage',
                'homeImage',
                'aboutImage',
                'galleriesImage',
                'servicesImage',
                'contactImage'
            )
        ));
    }
}