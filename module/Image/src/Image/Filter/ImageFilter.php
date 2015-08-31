<?php
namespace Image\Filter;

use Application\Filter\BaseFilter;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

/**
 * Class ImageFilter
 * @package Image\Filter
 */
class ImageFilter extends BaseFilter
{
    public function getMergedFilters(){
        return array_merge($this->getNameFilters(),$this->getImageFilters());
    }

    public function getNameFilters()
    {
        $vocabulary = $this->getVocabulary();
        return array(
            "name" => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_IMAGE_NAME_EMPTY"])
                            )
                        )
                    ),
//                    array(
//                        'name' => 'regex',
//                        'break_chain_on_failure' => true,
//                        'options' => array(
//                            'pattern' => '/^[\w,\s-]+$/',
//                            'messages' => array(
//                                Regex::NOT_MATCH => $this->getTranslator()->translate($vocabulary["ERROR_IMAGE_NAME_INVALID_PATTERN"])
//                            )
//                        )
//                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 50,
                            'messages' => array(
                                StringLength::TOO_LONG => $this->getTranslator()->translate($vocabulary["ERROR_IMAGE_NAME_INVALID_LENGTH"]),
                                StringLength::TOO_SHORT => $this->getTranslator()->translate($vocabulary["ERROR_IMAGE_NAME_INVALID_LENGTH"])
                            )
                        )
                    ),
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $this->getEntityManager()->getRepository('Image\Entity\Image'),
                            'fields' => 'name',
                            'messages' => array(
                                'objectFound' => $this->getTranslator()->translate($vocabulary["ERROR_IMAGE_EXISTS"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ));
    }

    public function getImageFilters()
    {
        $vocabulary = $this->getVocabulary();
        return array(
            "image" => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_IMAGE_EMPTY"])
                            )
                        )
                    ),
                ),
            )
        );
    }
}