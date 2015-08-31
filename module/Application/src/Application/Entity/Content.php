<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 22/6/2014
 * Time: 5:04 μμ
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Content
 * @package Application\Entity
 * @ORM\Entity
 * @ORM\Table(name="contents")
 */
class Content {

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="boolean", name="is_textarea")
     */
    private $isTextarea;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=25)
     */
    private $target;

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getIsTextarea()
    {
        return $this->isTextarea;
    }

    /**
     * @param mixed $isTextarea
     */
    public function setIsTextarea($isTextarea)
    {
        $this->isTextarea = $isTextarea;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }


} 