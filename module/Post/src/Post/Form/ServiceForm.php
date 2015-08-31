<?php
namespace Post\Form;


use Zend\Form\Form;

class ServiceForm extends Form{
    /**
     * The service form constructor
     */
    public function __construct(){
        parent::__construct("serviceForm");

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
            'service' => array(
                'name',
                'content',
                'snippet',
                'thumbnail',
            )
        ));
    }
}