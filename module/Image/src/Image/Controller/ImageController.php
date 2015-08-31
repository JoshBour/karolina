<?php
namespace Image\Controller;


use Application\Controller\BaseController;
use Zend\Http\Request;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class ImageController
 * @package Image\Controller
 * @method string translate($string)
 * @method Request getRequest()
 */
class ImageController extends BaseController
{
    const LAYOUT_ADMIN = "layout/admin";
    const ROUTE_ADD_IMAGE = "images/add";

    /**
     * The image repository
     *
     * @var \Image\Repository\ImageRepository
     */
    private $imageRepository;

    /**
     * The image form
     *
     * @var \Zend\Form\Form
     */
    private $imageForm;

    /**
     * The gallery repository
     *
     * @var \Image\Repository\GalleryRepository
     */
    private $galleryRepository;

    /**
     * Get the image service
     *
     * @var \Image\Service\ImageService
     */
    private $imageService;

    public function indexAction()
    {
        $page = $this->params()->fromRoute("page", 1);
        $paginator = new Paginator(new ArrayAdapter($this->getPostRepository()->findBy(array(), array("postDate" => "DESC"))));
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage(6);
        return new ViewModel(array(
            "pageTitle" => "Latest News",
            "bodyClass" => "postsPage blackLayout",
            "paginator" => $paginator,
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

            $service = $this->getImageService();


            $limit = $params->get("limit", 10);
            $page = $params->get("page", 1);
            $sort = $params->get("sort",null);
            $search = $params->get("search",null);


            $viewModel = new ViewModel(array(
                "paginator" => $service->load($limit, $page, $sort, $search),
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
                "paginator" => $this->getImageService()->load()
            ));
        }
        return $this->notFoundAction();
    }

    public function addAction()
    {
        $request = $this->getRequest();
        if ($this->identity()) {
            $this->layout()->setTemplate(self::LAYOUT_ADMIN);
            $form = $this->getImageForm();
            if ($request->isPost()) {
                $service = $this->getImageService();
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );
                if ($service->create($data, $form)) {
                    $this->flashMessenger()->addMessage($service->getMessage());

                    return $this->redirect()->toRoute(self::ROUTE_ADD_IMAGE);
                }
            }
            return new ViewModel(array(
                "form" => $form,
                "activeRoute" => "images",
                "pageTitle" => "Add Image"
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
            $service = $this->getImageService();
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
            $service = $this->getImageService();
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
     * Get the image form
     *
     * @return \Zend\Form\Form
     */
    public function getImageForm()
    {
        if (null === $this->imageForm)
            $this->imageForm = $this->getForm('image');
        return $this->imageForm;
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
     * Get the image service
     *
     * @return \Image\Service\ImageService
     */
    public function getImageService()
    {
        if (null === $this->imageService)
            $this->imageService = $this->getService('image');
        return $this->imageService;
    }
}