<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:21 πμ
 */

namespace Post\Controller;


use Application\Controller\BaseController;
use Zend\Http\Request;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class ServiceController
 * @package Post\Controller
 * @method string translate($string)
 * @method Request getRequest()
 */
class ServiceController extends BaseController
{
    const LAYOUT_ADMIN = "layout/admin";
    const ROUTE_ADD_SERVICE = "services/add";

    /**
     * The service form
     *
     * @var \Zend\Form\Form
     */
    private $serviceForm;

    /**
     * The service repository
     *
     * @var \Post\Repository\ServiceRepository
     */
    private $serviceRepository;

    /**
     * Get the service service (dat name choice)
     *
     * @var \Post\Service\ServiceService
     */
    private $serviceService;

    public function indexAction()
    {
        return new ViewModel(array(
            "pageTitle" => "Services",
            "services" => $this->getServiceRepository()->findAll()
        ));
    }

    public function viewAction()
    {
        $url = $this->params()->fromRoute("serviceUrl");
        if ($url) {
            $service = $this->getServiceRepository()->findOneBy(array("url" => $url));
            if ($service) {
                return new ViewModel(array(
                    "service" => $service,
                    "activeRoute" => "services_index",
                    "pageTitle" => $service->getName()
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

            $service = $this->getServiceService();


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
                "paginator" => $this->getServiceService()->load(),
            ));
        }
        return $this->notFoundAction();
    }

    public function addAction()
    {
        $request = $this->getRequest();
        if ($this->identity()) {
            $this->layout()->setTemplate(self::LAYOUT_ADMIN);
            $form = $this->getServiceForm();
            if ($request->isPost()) {
                $service = $this->getServiceService();
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );
                if ($service->create($data, $form)) {
                    $this->flashMessenger()->addMessage($service->getMessage());

                    return $this->redirect()->toRoute(self::ROUTE_ADD_SERVICE);
                }
            }
            return new ViewModel(array(
                "form" => $form,
                "activeRoute" => "services",
                "pageTitle" => "Add Service"
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
            $service = $this->getServiceService();
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
            $service = $this->getServiceService();
            $success = $service->remove($id) ? 1:0;
            return new JsonModel(array(
                "success" => $success,
                "message" => $service->getMessage()
            ));
        }
        return $this->notFoundAction();
    }

    /**
     * Get the service form
     *
     * @return \Zend\Form\Form
     */
    public function getServiceForm()
    {
        if (null === $this->serviceForm)
            $this->serviceForm = $this->getForm('service');
        return $this->serviceForm;
    }

    /**
     * Get the service repository
     *
     * @return \Post\Repository\ServiceRepository
     */
    public function getServiceRepository()
    {
        if (null === $this->serviceRepository)
            $this->serviceRepository = $this->getRepository('post', 'service');
        return $this->serviceRepository;
    }

    /**
     * Get the service service
     *
     * @return \Post\Service\ServiceService
     */
    public function getServiceService()
    {
        if (null === $this->serviceService)
            $this->serviceService = $this->getService('service');
        return $this->serviceService;
    }
}