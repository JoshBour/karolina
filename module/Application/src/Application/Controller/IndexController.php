<?php
namespace Application\Controller;

use Application\Model\SitemapXmlParser;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Http\Response\Stream;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class IndexController
 * @package Application\Controller
 * @method Request getRequest()
 * @method Response getResponse()
 */
class IndexController extends BaseController
{
    private $contentRepository;

    private $postRepository;

    public function homeAction()
    {
        $posts = $this->getPostRepository()->findBy(array(), array("postDate" => "DESC"), 4);
        return new ViewModel(array(
            "posts" => $posts,
            "contents" => $this->getContentRepository()->findAll(),
            "useBlackLayout" => true,
            "bodyClass" => "homePage"
        ));
    }

    public function aboutAction()
    {
        return new ViewModel(array(
            "pageTitle" => "About Us"
        ));
    }

    public function contactAction()
    {
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        $form = $this->getForm('contact');
        $vocabulary = $this->getVocabulary();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $applicationService = $this->getService('application');
                $result = $applicationService->sendMail($data);
                if ($result) {
                    $this->flashMessenger()->addMessage($vocabulary["EMAIL_SUCCESS"]);
                } else {
                    $this->flashMessenger()->addMessage($applicationService->getMessage());
                }
                return $this->redirect()->toRoute('contact');
            }

        }
        return new ViewModel(array(
            "form" => $form,
            "content" => $this->getRepository('application', 'content')->findOneBy(array("target" => "contact")),
            "useBlackLayout" => true,
            "bodyClass" => "contactPage blackLayout",
            "pageTitle" => "Info - Contact Us"
        ));
    }

    public function sitemapAction()
    {
        $this->getResponse()->getHeaders()->addHeaders(array('Content-type' => 'application/xml; charset=utf-8'));
        $type = $this->params()->fromRoute('type');
        $sitemapXmlParser = new SitemapXmlParser();
        $sitemapXmlParser->begin();
        if (!$type) {
            $sitemapXmlParser->addHeader("sitemapindex");
            $sitemapXmlParser->addSitemap("http://www.infolightingco.com/sitemap/static");
            $sitemapXmlParser->addSitemap("http://www.infolightingco.com/sitemap/dynamic/products");
            $sitemapXmlParser->addSitemap("http://www.infolightingco.com/sitemap/dynamic/posts");
        } else {
            if ($type == "static") {
                $pages = $this->getServiceLocator()->get('Config')['static_pages'];
                $sitemapXmlParser->addHeader("urlset");
                foreach ($pages as $page) {
                    $sitemapXmlParser->addUrl("http://www.infolightingco.com" . $page, "1.0");
                }
            } else {
                $index = $this->params()->fromRoute("index");

                $entities = $index == "products" ? $this->getProductRepository()->findAll() : $this->getPostRepository()->findAll();

                $sitemapXmlParser->addHeader("urlset", true);
                $i = 0;
                foreach ($entities as $entity) {
                    if ($i == 20) {
                        $sitemapXmlParser->show();
                        $i = 0;
                    }
                    $sitemapXmlParser->addEntityInfo($entity);
                    $i++;
                }
            }

        }
        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('application/index/sitemap.xml');
        $sitemapXmlParser->close();
        $sitemapXmlParser->show();
        return $view;
    }

    public function downloadFileAction()
    {
        $fileName = urldecode($this->params()->fromRoute("fileName", null));
        echo ROOT_PATH . '/' . $fileName;
        var_dump(file_exists(ROOT_PATH . '/' . $fileName));
        if (!$fileName || !file_exists(ROOT_PATH . '/' . $fileName)) {
            return $this->notFoundAction();
        } else {
            $pathInfo = pathinfo($fileName);
            switch ($pathInfo['extension']) {
                case "pdf":
                    $type = "application/pdf";
                    break;
                case "xls":
                    $type = "application/vnd.ms-excel";
                    break;
                case "zip":
                    $type = "application/zip";
                    break;
                case "ppt":
                    $type = "application/vnd.ms-powerpoint";
                    break;
                default:
                    $type = "text/plain";
            }
            $response = new Stream();
            $response->setStream(fopen($fileName, 'r'));
            $response->setStatusCode(200);

            $headers = new \Zend\Http\Headers();
            $headers->addHeaderLine('Content-Type', $type)
                ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->addHeaderLine('Content-Length', filesize($fileName));

            $response->setHeaders($headers);
            return $response;
        }
    }

    public function uploadFileAction()
    {
        $request = $this->getRequest();
        // this check is used for older web browsers, so that we can return the correct response
        $xhr = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        if ($this->identity()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $fileService = $this->getService('fileUtil');
            $result =  $fileService->upload($data);
            $success = $result ? 1 : 0;
            $response = array("success" => $success, "message" => $fileService->getMessage(), "result" => $result);
            if ($xhr) {
                return new JsonModel($response);
            } else {
                $viewModel = new ViewModel(array(
                    "result" => json_encode($response),
                ));
                $viewModel->setTerminal(true);

                return $viewModel;
            }
        }
        return $this->notFoundAction();
    }

    /**
     * The change language action
     * Route: /change-language/:lang
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function changeLanguageAction()
    {
        $session = new Container("base");
        $response = $this->getResponse();
        $lang = $this->params()->fromRoute('lang');
        if ($lang == 'en') {
            $session->locale = 'en_US';
        } else if ($lang == 'el') {
            $session->locale = 'el_GR';
        } else {
            $session->locale = null;
        }
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        $this->redirect()->toUrl($url);
        return $response;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getContentRepository()
    {
        if (null == $this->contentRepository)
            $this->contentRepository = $this->getRepository('application', 'content');
        return $this->contentRepository;
    }

    /**
     * @return \Post\Repository\PostRepository
     */
    public function getPostRepository()
    {
        if (null == $this->postRepository)
            $this->postRepository = $this->getRepository('post', 'post');
        return $this->postRepository;
    }

}
