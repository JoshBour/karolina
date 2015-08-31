<?php
namespace Post\Filter;

use Application\Filter\BaseFilter;
use DoctrineModule\Validator\ObjectExists;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

/**
 * Class CategoryFilter
 * @package Product\Filter
 */
class ServiceFilter extends BaseFilter
{
    public function getMergedFilters(){
        return array_merge($this->getNameFilters(),$this->getContentFilters(),$this->getSnippetFilters(),$this->getThumbnailFilters());
    }

    protected function getNameFilters()
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
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_SERVICE_NAME_EMPTY"])
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 50,
                            'messages' => array(
                                StringLength::TOO_LONG => $this->getTranslator()->translate($vocabulary["ERROR_SERVICE_NAME_INVALID_LENGTH"]),
                                StringLength::TOO_SHORT => $this->getTranslator()->translate($vocabulary["ERROR_SERVICE_NAME_INVALID_LENGTH"])
                            )
                        )
                    ),
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $this->getEntityManager()->getRepository('Post\Entity\Service'),
                            'fields' => 'name',
                            'messages' => array(
                                'objectFound' => $this->getTranslator()->translate($vocabulary["ERROR_SERVICE_EXISTS"])
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

    public function getContentFilters()
    {
        $vocabulary = $this->getVocabulary();
        return array(
            "content" => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_SERVICE_CONTENT_EMPTY"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
//                    array('name' => 'StripTags')
                )
            )
        );
    }

    public function getSnippetFilters()
    {
        $vocabulary = $this->getVocabulary();
        return array(
            "snippet" => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_SERVICE_SNIPPET_EMPTY"])
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 60,
                            'messages' => array(
                                StringLength::TOO_LONG => $this->getTranslator()->translate($vocabulary["ERROR_SERVICE_SNIPPET_INVALID_LENGTH"]),
                                StringLength::TOO_SHORT => $this->getTranslator()->translate($vocabulary["ERROR_SERVICE_SNIPPET_INVALID_LENGTH"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
//                    array('name' => 'StripTags')
                )
            )
        );
    }

    public function getThumbnailFilters()
    {
        $vocabulary = $this->getVocabulary();
        return array(
            "thumbnail" => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_THUMBNAIL_EMPTY"])
                            )
                        )
                    ),
                ),
            )
        );
    }
}