<?php
namespace Image\Controller;


use Application\Controller\BaseController;
use Zend\Http\Request;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class GalleryController
 * @package Image\Controller
 * @method string translate($string)
 * @method Request getRequest()
 */
class GalleryController extends BaseController
{
    const LAYOUT_ADMIN = "layout/admin";
    const ROUTE_ADD_GALLERY = "galleries/add";

    /**
     * The image repository
     *
     * @var \Image\Repository\ImageRepository
     */
    private $imageRepository;

    /**
     * The post form
     *
     * @var \Zend\Form\Form
     */
    private $galleryForm;

    /**
     * The gallery repository
     *
     * @var \Image\Repository\GalleryRepository
     */
    private $galleryRepository;

    /**
     * Get the post service
     *
     * @var \Image\Service\GalleryService
     */
    private $galleryService;

    public function indexAction()
    {
        $galleryName = $this->params('galleryName',null);
        $galleryRepository = $this->getGalleryRepository();

        $galleries = $galleryRepository->findAll();
        if(!empty($galleryName)){
            $activeGallery = $galleryRepository->findOneBy(array("url" => $galleryName));
            if(!$activeGallery)
                return $this->notFoundAction();
        }else{
            $activeGallery = count($galleries) > 0 ? $galleries[0] : null;
        }

        return new ViewModel(array(
            "pageTitle" => "Galleries",
            "galleries" => $galleries,
            "activeGallery" => $activeGallery
        ));
    }

    public function viewAction()
    {
        $url = $this->params()->fromRoute("url");
        if ($url) {
            $entity = $this->getGalleryRepository()->findOneBy(array("url" => $url));
            if ($entity) {
                return new ViewModel(array(
                    "bodyClass" => "blackLayout",
                    "post" => $entity,
                    "activeRoute" => "posts_index",
                    "pageTitle" => $entity->getTitle()
                ));
            }
        }
        return $this->notFoundAction();
    }

    public function loadAction()
    {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest() && $this->identity()) {
            $params = $request->getQuery();

            $service = $this->getGalleryService();


            $limit = $params->get("limit", 10);
            $page = $params->get("page", 1);
            $sort = $params->get("sort",null);
            $search = $params->get("search",null);


            $viewModel = new ViewModel(array(
                "paginator" => $service->load($limit, $page, $sort, $search),
                "images" => $this->getImageRepository()->findAll(),
                "galleryAssoc" => $this->getGalleryRepository()->findAssoc()
            ));
            $viewModel->setTerminal(true);
            return $viewModel;
        }
        return $this->notFoundAction();
    }

    public function listAction()
    {
        if ($this->identity()) {
            $this->layout()->setTemplate(self::LAYOUT_ADMIN);
            return new ViewModel(array(
                "paginator" => $this->getGalleryService()->load(),
                "images" => $this->getImageRepository()->findAll(),
                "galleryAssoc" => $this->getGalleryRepository()->findAssoc()
            ));
        }
        return $this->notFoundAction();
    }

    public function addAction()
    {
        $request = $this->getRequest();
        if ($this->identity()) {
            $this->layout()->setTemplate(self::LAYOUT_ADMIN);
            $form = $this->getGalleryForm();
            if ($request->isPost()) {
                $service = $this->getGalleryService();
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );
                if ($service->create($data, $form)) {
                    $this->flashMessenger()->addMessage($service->getMessage());

                    return $this->redirect()->toRoute(self::ROUTE_ADD_GALLERY);
                }else{
                    echo $service->getMessage();
                }
            }
            return new ViewModel(array(
                "form" => $form,
                "encodedImages" => isset($data['gallery']['images']) ? $data['gallery']['images'] : array(),
                "images" => $this->getImageRepository()->findAll(),
                "activeRoute" => "galleries",
                "pageTitle" => "Add Gallery"
            ));
        }
        return $this->notFoundAction();
    }

    public function saveAction()
    {
        /**
         * @var \Zend\Http\Request $request
         */
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest() && $this->identity()) {
            $service = $this->getGalleryService();
            $data = $request->getPost();
            $success = $service->save($data) ? 1 : 0;
            return new JsonModel(array(
                "success" => $success,
                "message" => $service->getMessage()
            ));
        } else {
            return $this->notFoundAction();
        }
    }

    public function deleteAction()
    {
        /**
         * @var \Zend\Http\Request $request
         */
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest() && $this->identity()) {
            $id = $this->params()->fromPost("entityId");
            $service = $this->getGalleryService();
            $success = $service->remove($id) ? 1:0;
            return new JsonModel(array(
                "success" => $success,
                "message" => $service->getMessage()
            ));
        }
        return $this->notFoundAction();
    }

    /**
     * Get the image repository
     *
     * @return \Image\Repository\ImageRepository
     */
    public function getImageRepository()
    {
        if (null === $this->imageRepository)
            $this->imageRepository = $this->getRepository('image', 'image');
        return $this->imageRepository;
    }

    /**
     * Get the gallery form
     *
     * @return \Zend\Form\Form
     */
    public function getGalleryForm()
    {
        if (null === $this->galleryForm)
            $this->galleryForm = $this->getForm('gallery');
        return $this->galleryForm;
    }

    /**
     * Get the gallery repository
     *
     * @return \Image\Repository\GalleryRepository
     */
    public function getGalleryRepository()
    {
        if (null === $this->galleryRepository)
            $this->galleryRepository = $this->getRepository('image', 'gallery');
        return $this->galleryRepository;
    }

    /**
     * Get the gallery service
     *
     * @return \Image\Service\GalleryService
     */
    public function getGalleryService()
    {
        if (null === $this->galleryService)
            $this->galleryService = $this->getService('gallery');
        return $this->galleryService;
    }
}