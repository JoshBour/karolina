<?php
namespace Image\Service;


use Application\Service\BaseService;
use Doctrine\Common\Collections\ArrayCollection;
use Image\Entity\Gallery as GalleryEntity;
use Image\Entity\GalleryImage;
use Zend\Form\Form;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\Stdlib\ParametersInterface;

/**
 * Class GalleryService
 * @package Image\Service
 */
class GalleryService extends BaseService
{

    /**
     * The gallery repository
     *
     * @var \Image\Repository\GalleryRepository
     */
    private $galleryRepository;

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
        $repository = $this->getGalleryRepository();
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
     * Create a new gallery
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create(&$data, &$form)
    {
        $entity = new GalleryEntity();
        $em = $this->getEntityManager();

        if (isset($data['gallery']['images'])) {
            $data['gallery']['images'] = json_decode($data['gallery']['images'], true);
        }

        $form->bind($entity);
        $form->setData($data);
        $form->setBindOnValidate(Form::BIND_MANUAL);
        if (!$form->isValid()) return false;

        $entity->setName($data['gallery']['name']);
        $entity->setUrl($this->getGalleryUrl($entity));

        if (isset($data['gallery']['parentGallery']) && !empty($data['gallery']['parentGallery'])) {
            $parent = $this->getGalleryRepository()->find($data['gallery']['parentGallery']);
            if ($parent)
                $entity->setParentGallery($parent);
        }

        // WHY THE FUCK DO I HAVE TO FLUSH HERE
        $em->persist($entity);
        $em->flush();
        foreach ($data['gallery']['images'] as $join) {
            $image = $this->getImageRepository()->find($join['joinId']);
            if ($image) {
                $galleryImage = new GalleryImage($join['title'], $join['position']);
                $image->addGalleries($galleryImage);
                $entity->addImages($galleryImage);
            }
        }

        try {
            $em->persist($entity);
            $em->flush();
            $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_GALLERY_CREATED"]);
            return true;
        } catch (\Exception $e) {
            $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_GALLERY_NOT_CREATED"]);
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
        $repository = $this->getGalleryRepository();
        $skipAssign = false;
        if ($constraints)
            $constraints = json_decode(str_replace('\\', '\\\\', $constraints), true);

        if ($primaryKey) {
            /**
             * @var \Image\Entity\Gallery $entity
             */
            $entity = $repository->find($primaryKey);
            if ($entity) {
                /**
                 * @var \Zend\InputFilter\InputFilterInterface $filter
                 */
                $filter = $this->getServiceManager()->get('galleryFilter')->filter($attribute, $content);
                if ($filter->isValid()) {
                    if (!empty($constraints) && $content) {
                        foreach ($constraints as $constraint) {
                            if ($constraint["type"] == "foreign") {
                                $content = $em->getReference($constraint["target"], $content);
                            }
                        }
                    }

                    if ($attribute == "images") {
                        $joins = json_decode($content, true);
                        // after we remove the images, we flash the entity
                        $entity->clearImages();
                        $em->persist($entity);
                        $em->flush();
                        $skipAssign = true;
                        foreach($joins as $join){
                            $image = $this->getImageRepository()->find($join["joinId"]);
                            $galleryImage = $this->getRepository('image', 'galleryImage')->findOneBy(array("image" => $image, "gallery" => $entity));
                            if($galleryImage){
                                $galleryImage->setTitle($join['title']);
                                $galleryImage->setPosition($join['position']);
                            }else{
                                $galleryImage = new GalleryImage($join['title'], $join['position']);
                                $image->addGalleries($galleryImage);
                            }
                            $entity->addImages($galleryImage);
                        }
                    }

                    if (!$skipAssign)
                        $entity->{'set' . ucfirst($attribute)}($content);

                    if($attribute == "name"){
                        $entity->setUrl($this->getGalleryUrl($entity));
                    }
                    try {
                        $em->persist($entity);
                        $em->flush();
                        $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_GALLERY_SAVED"]);
                        return true;
                    } catch (\Exception  $e) {
                        $this->message = $this->getTranslator()->translate($this->getVocabulary()["ERROR_GALLERY_NOT_SAVED"]);
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
        $entity = $this->getGalleryRepository()->find($id);
        if ($entity) {
            try {
                $em->remove($entity);
                $em->flush();
                $this->message = $this->getTranslator()->translate($this->getVocabulary()["MESSAGE_GALLERY_REMOVED"]);
                return true;
            } catch (\Exception $e) {
                $this->message = $this->getTranslator()->translate($this->getVocabulary()["ERROR_GALLERY_NOT_REMOVED"]);
                return false;
            }
        }
        return false;
    }

    /**
     * Get the gallery url
     *
     * @param \Image\Entity\Gallery $gallery
     * @return string
     */
    private function getGalleryUrl($gallery)
    {
        $encodedUrl = $gallery->encodeUrl();
        $galleries = $this->getGalleryRepository()->findBy(array("url" => $encodedUrl));
        return count($galleries) > 0 ? $encodedUrl . '-' . (count($galleries) + 1) : $encodedUrl;
    }

    /**
     * Get the gallery repository
     *
     * @return \Image\Repository\GalleryRepository
     */
    private function getGalleryRepository()
    {
        if (null === $this->galleryRepository)
            $this->galleryRepository = $this->getRepository('image', 'gallery');
        return $this->galleryRepository;
    }

    /**
     * Get the gallery repository
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