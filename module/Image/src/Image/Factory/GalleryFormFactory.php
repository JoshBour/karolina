<?php
namespace Image\Factory;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Image\Entity\Gallery;
use Image\Form\GalleryFieldset;
use Image\Form\GalleryForm;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GalleryFormFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $entityManager
         */
        $entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $formManager = $serviceLocator->get('FormElementManager');
        /**
         * @var GalleryFieldset $fieldset
         */
        $fieldset = $formManager->get('Image\Form\GalleryFieldset');
        $form = new GalleryForm();
        $hydrator = new DoctrineHydrator($entityManager, '\Image\Entity\Gallery');

        $fieldset->setUseAsBaseFieldset(true)
            ->setHydrator($hydrator)
            ->setObject(new Gallery());

        $form->add($fieldset)
            ->setInputFilter(new InputFilter())
            ->setHydrator($hydrator);

        return $form;
    }
}