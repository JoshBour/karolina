<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 16/3/2014
 * Time: 8:15 μμ
 */

namespace Application\Factory;

use Application\Form\ContactFieldset;
use Application\Form\ContactForm;
use Application\Form\SettingForm;
use Post\Form\ServiceForm;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SettingFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $formManager = $serviceLocator->get('FormElementManager');
        /**
         * @var ContactFieldset $fieldset
         */
        $fieldset = $formManager->get('Application\Form\SettingFieldset');
        $form = new SettingForm();

        $form->add($fieldset)
            ->setInputFilter(new InputFilter());
        return $form;
    }

}

