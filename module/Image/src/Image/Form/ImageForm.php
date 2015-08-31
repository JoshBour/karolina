<?php
namespace Image\Form;


use Zend\Form\Form;

class ImageForm extends Form{
    /**
     * The image form constructor
     */
    public function __construct(){
        parent::__construct("imageForm");

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
            'image' => array(
                'name',
                'image',
            )
        ));
    }
}