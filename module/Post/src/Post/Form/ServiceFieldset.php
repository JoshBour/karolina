<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:56 πμ
 */

namespace Post\Form;


use Application\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class ServiceFieldset extends BaseFieldset implements InputFilterProviderInterface
{

    /**
     * The post fieldset initializer
     */
    public function init()
    {
        parent::__construct("service");

        $vocabulary = $this->getVocabulary();

        $this->add(array(
            'name' => 'thumbnail',
            'type' => 'file',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_SERVICE_THUMBNAIL"])
            ),
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_SERVICE_NAME"])
            ),
            'attributes' => array(
                'placeholder' => $this->getTranslator()->translate($vocabulary["PLACEHOLDER_SERVICE_NAME"])
            ),

        ));

        $this->add(array(
            'name' => 'content',
            'type' => 'text',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_SERVICE_CONTENT"])
            ),
            'attributes' => array(
                'placeholder' => $this->getTranslator()->translate($vocabulary["PLACEHOLDER_SERVICE_CONTENT"])
            ),
        ));

        $this->add(array(
            'name' => 'snippet',
            'type' => 'text',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_SERVICE_SNIPPET"])
            ),
            'attributes' => array(
                'placeholder' => $this->getTranslator()->translate($vocabulary["PLACEHOLDER_SERVICE_SNIPPET"])
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
        $filters = $this->getServiceLocator()->get('serviceFilter')->getMergedFilters();
        $filters["thumbnail"]["validators"][] = array(
            'name' => 'Application\Validator\Image',
            'options' => array(
                'maxSize' => '40960',
            )
        );
        return $filters;
    }


}