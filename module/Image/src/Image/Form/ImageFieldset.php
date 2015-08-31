<?php
namespace Image\Form;


use Application\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class ImageFieldset extends BaseFieldset implements InputFilterProviderInterface
{

    /**
     * The image fieldset initializer
     */
    public function init()
    {
        parent::__construct("image");

        $vocabulary = $this->getVocabulary();

        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_IMAGE_NAME"])
            ),
            'attributes' => array(
                'placeholder' => $this->getTranslator()->translate($vocabulary["PLACEHOLDER_IMAGE_NAME"])
            ),

        ));

        $this->add(array(
            'name' => 'image',
            'type' => 'file',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_IMAGE_IMAGE"])
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
        $filters = $this->getServiceLocator()->get('imageFilter')->getMergedFilters();
        $filters["image"]["validators"][] = array(
            'name' => 'Application\Validator\Image',
            'options' => array(
                'maxSize' => '40960',
            )
        );
        return $filters;
    }


}