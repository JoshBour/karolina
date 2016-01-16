<?php
namespace Image\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Image
 * @package Image\Entity
 * @ORM\Entity(repositoryClass="\Image\Repository\ImageRepository")
 * @ORM\Table(name="images")
 */
class Image{

    public static $thumbnailVariations = array(
        "admin" => array("width" => 100, "height" => 70),
        "list" => array('width' => 320, "height" => 240));

    /**
     * @ORM\OneToMany(targetEntity="GalleryImage", mappedBy="image", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $galleries;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=5, name="image_id")
     */
    private $imageId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    public function __construct(){
        $this->galleries = new ArrayCollection();
    }

    public function addGalleries(GalleryImage $gallery){
        if(!$this->galleries->contains($gallery)){
            $this->galleries->add($gallery);
            $gallery->setImage($this);
        }
    }

    public function clearGalleries(){
        $this->galleries->clear();
        foreach($this->galleries as $gallery)
            $gallery->setImage(null);
    }

    public function removeGalleries(GalleryImage $gallery){
        if($this->galleries->contains($gallery)){
            $this->galleries->removeElement($gallery);
            $gallery->setImage(null);
        }
    }

    /**
     * @return mixed
     */
    public function getGalleries()
    {
        return $this->galleries;
    }

    /**
     * @param mixed $galleries
     */
    public function setGalleries($galleries)
    {
        $this->galleries = $galleries;
    }

    /**
     * @param string $type
     * @return string
     */
    public function getImage($type = null)
    {
        if (!$type || !in_array($type,array_keys(self::$thumbnailVariations))) {
            return $this->image;
        } else {
            $split = explode('.', $this->image);
            $dimensions = self::$thumbnailVariations[$type];
            return $split[0] . '-' . $dimensions["width"] . 'x' . $dimensions["height"] . '.' . $split[1];
        }
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
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * @param mixed $imageId
     */
    public function setImageId($imageId)
    {
        $this->imageId = $imageId;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


}