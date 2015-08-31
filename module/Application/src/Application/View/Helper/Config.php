<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Config extends AbstractHelper {

    protected $serviceManager;

    public function __construct($serviceManager) {
        $this->serviceManager = $serviceManager;
    }

    public function __invoke() {
        $config = $this->serviceManager->get('Config');
        return $config;
    }

}