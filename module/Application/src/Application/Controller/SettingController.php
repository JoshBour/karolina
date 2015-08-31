<?php
namespace Application\Controller;

use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;

/**
 * Class SettingController
 * @package Application\Controller
 * @method string translate($string)
 * @method Request getRequest()
 */
class SettingController extends BaseController
{
    const LAYOUT_ADMIN = "layout/admin";
    const ROUTE_SETTINGS = "settings";

    public function listAction()
    {
        $request = $this->getRequest();
        if ($this->identity()) {
            $this->layout()->setTemplate(self::LAYOUT_ADMIN);
            $form = $this->getForm('setting');
            if ($request->isPost()) {
                $service = $this->getService('setting');
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );
                if ($service->create($data, $form)) {
                    $this->flashMessenger()->addMessage($service->getMessage());

                    return $this->redirect()->toRoute(self::ROUTE_SETTINGS);
                }
            }
            return new ViewModel(array(
                "form" => $form,
            ));
        }
        return $this->notFoundAction();
    }


}
