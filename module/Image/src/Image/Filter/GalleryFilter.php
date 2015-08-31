<?php
namespace Image\Filter;

use Application\Filter\BaseFilter;
use DoctrineModule\Validator\ObjectExists;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * Class GalleryFilter
 * @package Image\Filter
 */
class GalleryFilter extends BaseFilter
{
    public function getMergedFilters(){
        return array_merge($this->getImagesFilters(),$this->getNameFilters(),$this->getParentGalleryFilters());
    }

    public function getImagesFilters(){
        $vocabulary = $this->getVocabulary();
        return array(
            'images' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_GALLERY_IMAGES_EMPTY"])
                            )
                        )
                    ),
                    array(
                        'name' => 'Image\Validator\Gallery',
                        'break_chain_on_failure' => true,
                    ),
                ),
            )
        );
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
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_GALLERY_NAME_EMPTY"])
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 50,
                            'messages' => array(
                                StringLength::TOO_LONG => $this->getTranslator()->translate($vocabulary["ERROR_GALLERY_NAME_INVALID_LENGTH"]),
                                StringLength::TOO_SHORT => $this->getTranslator()->translate($vocabulary["ERROR_GALLERY_NAME_INVALID_LENGTH"])
                            )
                        )
                    ),
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $this->getEntityManager()->getRepository('Image\Entity\Gallery'),
                            'fields' => 'name',
                            'messages' => array(
                                'objectFound' => $this->getTranslator()->translate($vocabulary["ERROR_GALLERY_EXISTS"])
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

    public function getParentGalleryFilters()
    {
        $vocabulary = $this->getVocabulary();
        return array(
            "parentGallery" => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'DoctrineModule\Validator\ObjectExists',
                        'options' => array(
                            'object_repository' => $this->getEntityManager()->getRepository('Image\Entity\Gallery'),
                            'fields' => 'galleryId',
                            'messages' => array(
                                ObjectExists::ERROR_NO_OBJECT_FOUND => $this->getTranslator()->translate($vocabulary["ERROR_GALLERY_NOT_EXISTS"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            )
        );
    }
}