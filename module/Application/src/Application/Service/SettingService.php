<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:48 πμ
 */

namespace Application\Service;


use Doctrine\ORM\EntityRepository;
use Zend\Stdlib\ParametersInterface;

/**
 * Class SettingService
 * @package Application\Service
 */
class SettingService extends BaseService
{

    /**
     * @param $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data, &$form)
    {
        $form->setData($data);
        if (!$form->isValid()) return false;

        if (!empty($data['setting']['aboutProfileImage']['name'])) {
            if (!$newName = FileUtilService::rename($data['setting']['aboutProfileImage'], 'images', null, "about-profile")) {
                $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_SETTINGS_NOT_CREATED"]);
                return false;
            }else{
                FileUtilService::resize($newName, 'images', 400, null, 'png');
                FileUtilService::deleteFile(ROOT_PATH . '/images/' . $newName);
            }
        }

        if (!empty($data['setting']['homeImage']['name'])) {
            if (!FileUtilService::rename($data['setting']['homeImage'], 'images', null, "home-side-bg")) {
                $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_SETTINGS_NOT_CREATED"]);
                return false;
            }
        }

        if (!empty($data['setting']['aboutImage']['name'])) {
            if (!FileUtilService::rename($data['setting']['aboutImage'], 'images', null, "about-side-bg")) {
                $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_SETTINGS_NOT_CREATED"]);
                return false;
            }
        }

        if (!empty($data['setting']['galleriesImage']['name'])) {
            if (!FileUtilService::rename($data['setting']['galleriesImage'], 'images', null, "galleries-side-bg")) {
                $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_SETTINGS_NOT_CREATED"]);
                return false;
            }
        }

        if (!empty($data['setting']['servicesImage']['name'])) {
            if (!FileUtilService::rename($data['setting']['servicesImage'], 'images', null, "services-side-bg")) {
                $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_SETTINGS_NOT_CREATED"]);
                return false;
            }
        }

        if (!empty($data['setting']['contactImage']['name'])) {
            if (!FileUtilService::rename($data['setting']['contactImage'], 'images', null, "contact-side-bg")) {
                $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_SETTINGS_NOT_CREATED"]);
                return false;
            }
        }
        $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_SETTINGS_UPDATED"]);
        return true;
    }
} 