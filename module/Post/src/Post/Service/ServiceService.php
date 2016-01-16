<?php
namespace Post\Service;


use Application\Service\BaseService;
use Application\Service\FileUtilService;
use Post\Entity\Service as ServiceEntity;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\Stdlib\ParametersInterface;

/**
 * Class Post
 * @package Post\Service
 */
class ServiceService extends BaseService{

    /**
     * The service repository
     *
     * @var \Post\Repository\ServiceRepository
     */
    private $serviceRepository;

    public function load($limit = 10, $page = 1, $sort = null, $search = null)
    {
        if(!empty($sort)){
            $params = explode(",",$sort);
            $sort = array("column" => $params[0],"type" => strtoupper($params[1]));
        }else{
            $sort = array();
        };
        $repository = $this->getServiceRepository();
        if (empty($search) || trim($search) == false) {
            // the findby uses a different format
            if(!empty($sort)) $sort = array($sort["column"] => $sort["type"]);

            $entities = $repository->findBy(array(), $sort);
            $paginator = new Paginator(new ArrayAdapter($entities));
            $paginator->setCurrentPageNumber($page)
                ->setDefaultItemCountPerPage($limit);

            return $paginator;
        } else {
            return $repository->search($search,$page,$limit,$sort);
        }
    }

    /**
     * Create a new post
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data, &$form){
        $entity = new ServiceEntity();
        $em = $this->getEntityManager();

        $form->bind($entity);
        $form->setData($data);
        if (!$form->isValid()) return false;

        // we rename the file with a unique name
        $newName = FileUtilService::rename($data['service']['thumbnail'], 'images/services', "service");

        foreach(ServiceEntity::$thumbnailVariations as $variation){
            $result = FileUtilService::resize($newName,'images/services', $variation["width"], $variation["height"]);
            if(!$result) {
                $this->message = $result;
                return false;
            }
        }

        $entity->setThumbnail($newName);
        $entity->setUrl($this->getServiceUrl($entity));
        try {
            $em->persist($entity);
            $em->flush();
            $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_SERVICE_CREATED"]);
            return true;
        } catch (\Exception $e) {
            $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_SERVICE_NOT_CREATED"]);
            return false;
        }
    }

    public function save(ParametersInterface $data)
    {
        $em = $this->getEntityManager();
        $attribute = $data->get('attribute');
        $primaryKey = $data->get('primaryKey');
        $content = $data->get('content') ? $data->get('content') : null;
        $constraints = $data->get('constraints', null);
        $repository = $this->getServiceRepository();
        if ($constraints)
            $constraints = json_decode(str_replace('\\', '\\\\', $constraints), true);

        if ($primaryKey) {
            $entity = $repository->find($primaryKey);
            if ($entity) {
                /**
                 * @var \Zend\InputFilter\InputFilterInterface $filter
                 */
                $filter = $this->getServiceManager()->get('serviceFilter')->filter($attribute, $content);
                if ($filter->isValid()) {
                    if (!empty($constraints) && $content) {
                        foreach ($constraints as $constraint) {
                            if ($constraint["type"] == "foreign") {
                                $content = $em->getReference($constraint["target"], $content);
                            }
                        }
                    }

                    if($attribute == "thumbnail"){
                        $this->deleteImages($entity);
                        foreach(ServiceEntity::$thumbnailVariations as $variation){
                            $result = FileUtilService::resize($content,'images/services', $variation["width"], $variation["height"]);
                            if(!$result) {
                                $this->message = $result;
                                return false;
                            }
                        }
                    }
                    $entity->{'set' . ucfirst($attribute)}($content);


                    if($attribute == "name"){
                        $entity->setUrl($this->getServiceUrl($entity));
                    }
                    try {
                        $em->persist($entity);
                        $em->flush();
                        $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_SERVICE_SAVED"]);
                        return true;
                    } catch (\Exception  $e) {
                        $this->message = $this->getTranslator()->translate($this->getVocabulary()["ERROR_SERVICE_NOT_SAVED"]);
                    }
                } else {
                    $this->message = $filter->getMessages()[$attribute];
                }
            }
        }
        return false;
    }


    /**
     * Removes a post from the database
     *
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        $em = $this->getEntityManager();
        $service = $this->getServiceRepository()->find($id);
        if ($service) {
            try {
                $this->deleteImages($service);
                $em->remove($service);
                $em->flush();
                $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_SERVICE_REMOVED"]);
                return true;
            } catch (\Exception $e) {
                $this->message = $this->getTranslator()->translate($this->getVocabulary()["ERROR_SERVICE_NOT_REMOVED"]);
                return false;
            }
        }
        return false;
    }

    private function deleteImages(ServiceEntity $entity)
    {
        if ($thumbnail = $entity->getThumbnail()) {
            FileUtilService::deleteFile(FileUtilService::getFilePath($thumbnail, 'services', 'public/images'));
            foreach (ServiceEntity::$thumbnailVariations as $key => $variation) {
                $variationImg = $entity->getThumbnail($key);
                FileUtilService::deleteFile(FileUtilService::getFilePath($variationImg, 'services', 'public/images'));
            }
        }
    }

    /**
     * Get the service url
     *
     * @param \Post\Entity\Service $service
     * @return string
     */
    private function getServiceUrl($service){
        $encodedUrl = $service->encodeUrl();
        $services = $this->getServiceRepository()->findBy(array("url" => $encodedUrl));
        return count($services) > 0 ? $encodedUrl.'-'.(count($services)+1):$encodedUrl;
    }

    /**
     * @return \Post\Repository\ServiceRepository
     */
    private function getServiceRepository(){
        if(null === $this->serviceRepository)
            $this->serviceRepository = $this->getRepository('post','service');
        return $this->serviceRepository;
    }
}