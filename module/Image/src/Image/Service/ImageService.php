<?php
namespace Image\Service;


use Application\Service\BaseService;
use Application\Service\FileUtilService;
use Image\Entity\Image as ImageEntity;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\Stdlib\ParametersInterface;

/**
 * Class ImageService
 * @package Image\Service
 */
class ImageService extends BaseService
{

    /**
     * The image repository
     *
     * @var \Image\Repository\ImageRepository
     */
    private $imageRepository;

    public function load($limit = 10, $page = 1, $sort = null, $search = null)
    {
        if (!empty($sort)) {
            $params = explode(",", $sort);
            $sort = array("column" => $params[0], "type" => strtoupper($params[1]));
        } else {
            $sort = array();
        };
        $repository = $this->getImageRepository();
        if (empty($search) || trim($search) == false) {
            // the findby uses a different format
            if (!empty($sort)) $sort = array($sort["column"] => $sort["type"]);

            $entities = $repository->findBy(array(), $sort);
            $paginator = new Paginator(new ArrayAdapter($entities));
            $paginator->setCurrentPageNumber($page)
                ->setDefaultItemCountPerPage($limit);

            return $paginator;
        } else {
            return $repository->search($search, $page, $limit, $sort);
        }
    }

    /**
     * Create a new image
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data, &$form)
    {
        $entity = new ImageEntity();
        $em = $this->getEntityManager();

        $form->bind($entity);
        $form->setData($data);
        if (!$form->isValid()) return false;

        // we rename the file with a unique name
        $newName = FileUtilService::rename($data['image']['image'], 'images/gallery', "gallery");
        foreach (ImageEntity::$thumbnailVariations as $variation) {
            $result = FileUtilService::resize($newName, 'images/gallery', $variation["width"], $variation["height"]);
            if (!$result) {
                $this->message = $result;
                return false;
            }
        }

        $entity->setImage($newName);
        try {
            $em->persist($entity);
            $em->flush();
            $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_IMAGE_CREATED"]);
            return true;
        } catch (\Exception $e) {
            $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_IMAGE_NOT_CREATED"]);
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
        $repository = $this->getImageRepository();
        if ($constraints)
            $constraints = json_decode(str_replace('\\', '\\\\', $constraints), true);

        if ($primaryKey) {
            $entity = $repository->find($primaryKey);
            if ($entity) {
                /**
                 * @var \Zend\InputFilter\InputFilterInterface $filter
                 */
                $filter = $this->getServiceManager()->get('imageFilter')->filter($attribute, $content);
                if ($filter->isValid()) {
                    if (!empty($constraints) && $content) {
                        foreach ($constraints as $constraint) {
                            if ($constraint["type"] == "foreign") {
                                $content = $em->getReference($constraint["target"], $content);
                            }
                        }
                    }
                    if ($attribute == "image") {
                        // delete the old images
                        $this->deleteImages($entity);
                        foreach (ImageEntity::$thumbnailVariations as $variation) {
                            $result = FileUtilService::resize($content, 'images/gallery', $variation["width"], $variation["height"]);
                            if (!$result) {
                                $this->message = $result;
                                return false;
                            }
                        }
                    }
                    $entity->{'set' . ucfirst($attribute)}($content);
                    try {
                        $em->persist($entity);
                        $em->flush();
                        $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_IMAGE_SAVED"]);
                        return true;
                    } catch (\Exception  $e) {
                        $this->message = $this->getTranslator()->translate($this->getVocabulary()["ERROR_IMAGE_NOT_SAVED"]);
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
        $entity = $this->getImageRepository()->find($id);
        if ($entity) {
            $this->deleteImages($entity);
            try {
                $em->remove($entity);
                $em->flush();
                $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_IMAGE_REMOVED"]);
                return true;
            } catch (\Exception $e) {
                $this->message = $this->getTranslator()->translate($this->getVocabulary()["ERROR_IMAGE_NOT_REMOVED"]);
                return false;
            }
        }
        return false;
    }

    private function deleteImages(ImageEntity $entity)
    {
        if ($image = $entity->getImage()) {
            FileUtilService::deleteFile(FileUtilService::getFilePath($image, 'gallery', 'images'));
            foreach (ImageEntity::$thumbnailVariations as $key => $variation) {
                $variationImg = $entity->getImage($key);
                FileUtilService::deleteFile(FileUtilService::getFilePath($variationImg, 'gallery', 'images'));
            }
        }
    }


    /**
     * Get the image repository
     *
     * @return \Image\Repository\ImageRepository
     */
    private function getImageRepository()
    {
        if (null === $this->imageRepository)
            $this->imageRepository = $this->getRepository('image', 'image');
        return $this->imageRepository;
    }
}