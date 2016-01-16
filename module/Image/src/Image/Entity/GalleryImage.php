<?php
namespace Image\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class GalleryImage
 * @package Image\Entity
 * @ORM\Entity
 * @ORM\Table(name="gallery_images")
 */
class GalleryImage {

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Gallery", inversedBy="images")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="gallery_id")
     */
    private $gallery;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="galleries")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="image_id")
     */
    private $image;

    /**
     * @ORM\Column(type="integer", length=3)
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $title;

    public function __construct($title, $position){
        $this->title = $title;
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * @param mixed $gallery
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }


}