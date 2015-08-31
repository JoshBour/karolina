<?php
namespace Image\Form;


use Application\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class GalleryFieldset extends BaseFieldset implements InputFilterProviderInterface
{

    /**
     * The post fieldset initializer
     */
    public function init()
    {
        parent::__construct("gallery");

        $vocabulary = $this->getVocabulary();

        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_GALLERY_NAME"])
            ),
            'attributes' => array(
                'placeholder' => $this->getTranslator()->translate($vocabulary["PLACEHOLDER_GALLERY_NAME"])
            ),

        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'images',
            'options' => array(
                'object_manager' => $this->getEntityManager(),
                'target_class' => 'Image\Entity\Image',
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_GALLERY_IMAGES"])
            ),
            'attributes' => array(
                'class' => 'imageSelect'
            )
        ));

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'parentGallery',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_GALLERY_PARENT_GALLERY"]),
                'object_manager' => $this->getEntityManager(),
                'empty_option' => $this->getTranslator()->translate($vocabulary["EMPTY_OPTION"]),
                'target_class' => 'Image\Entity\Gallery',
                'property' => 'name',
                'disable_inarray_validator' => true
            ),
        ));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return $this->getServiceLocator()->get('galleryFilter')->getMergedFilters();
    }


}