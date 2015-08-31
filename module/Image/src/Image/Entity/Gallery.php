<?php

namespace Image\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Gallery
 * @package Image\Entity
 * @ORM\Entity(repositoryClass="\Image\Repository\GalleryRepository")
 * @ORM\Table(name="galleries")
 */
class Gallery{

    /**
     * @ORM\OneToMany(targetEntity="Gallery", mappedBy="parentGallery")
     */
    private $subGalleries;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=3, name="gallery_id")
     */
    private $galleryId;

    /**
     * @ORM\OneToMany(targetEntity="GalleryImage", mappedBy="gallery", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $images;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Gallery", inversedBy="subGalleries")
     * @ORM\JoinColumn(name="parent_gallery_id", referencedColumnName="gallery_id")
     */
    private $parentGallery;

    /**
     * @ORM\Column(type="string")
     */
    private $url;

    public function __construct(){
        $this->images = new ArrayCollection();
        $this->subGalleries = new ArrayCollection();
    }

    public function encodeUrl(){
        $words = array_slice(str_word_count($this->name,1),0,10);
        foreach($words as $key => $word){
            $words[$key] = strtolower($word);
        }
        return join('-',$words);
    }

    /**
     * @return mixed
     */
    public function getSubGalleries()
    {
        return $this->subGalleries;
    }

    /**
     * @param mixed $subGalleries
     */
    public function setSubGalleries($subGalleries)
    {
        $this->subGalleries = $subGalleries;
    }

    /**
     * @return mixed
     */
    public function getGalleryId()
    {
        return $this->galleryId;
    }

    /**
     * @param mixed $galleryId
     */
    public function setGalleryId($galleryId)
    {
        $this->galleryId = $galleryId;
    }

    public function addImages(GalleryImage $image){
        if(!$this->images->contains($image)){
            $this->images->add($image);
            $image->setGallery($this);
        }
    }

    public function clearImages(){
        foreach($this->images as $image){
            $this->images->removeElement($image);
            $image->setGallery(null);
        }
    }

    public function removeImages(GalleryImage $image){
        if($this->images->contains($image)){
            $this->images->removeElement($image);
            $image->setGallery(null);
        }
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $images
     */
    public function setImages($images)
    {
        $this->images = $images;
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

    /**
     * @return mixed
     */
    public function getParentGallery()
    {
        return $this->parentGallery;
    }

    /**
     * @param mixed $parentGallery
     */
    public function setParentGallery($parentGallery)
    {
        $this->parentGallery = $parentGallery;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }



}