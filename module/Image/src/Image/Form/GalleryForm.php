<?php
namespace Image\Form;


use Zend\Form\Form;

class GalleryForm extends Form{
    /**
     * The post form constructor
     */
    public function __construct(){
        parent::__construct("galleryForm");

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
            'gallery' => array(
                'name',
                'images',
                'parentGallery',
            )
        ));
    }
}