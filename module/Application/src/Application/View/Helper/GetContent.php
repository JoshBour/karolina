<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class GetContent extends AbstractHelper {

    private $contents;

    protected $serviceManager;

    public function __construct($serviceManager) {
        $this->serviceManager = $serviceManager;
    }

    public function __invoke($target) {
        $contents = $this->getContents();
        foreach($contents as $content){
            if($content->getTarget() == $target)
                return $content->getContent();
        }
        return "";
    }

    private function getContents(){
        if(null === $this->contents)
            $this->contents = $this->serviceManager->get('Doctrine\ORM\EntityManager')->getRepository('Application\Entity\Content')->findAll();
        return $this->contents;
    }

}