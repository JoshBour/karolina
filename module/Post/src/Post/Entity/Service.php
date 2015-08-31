<?php
namespace Post\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Service
 * @package Post\Entity
 * @ORM\Entity(repositoryClass="\Post\Repository\ServiceRepository")
 * @ORM\Table(name="services")
 */
class Service
{
    public static $thumbnailVariations = array(
        "list" => array("width" => 300, "height" => 125),
        "admin" => array("width" => 100, "height" => 70));

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=2, name="service_id")
     */
    private $serviceId;

    /**
     * @ORM\Column(type="text")
     */
    private $snippet;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $thumbnail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    public function encodeUrl()
    {
        $words = array_slice(str_word_count($this->name, 1), 0, 10);
        foreach ($words as $key => $word) {
            $words[$key] = strtolower($word);
        }
        return join('-', $words);
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

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
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * @param mixed $serviceId
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;
    }

    /**
     * @return mixed
     */
    public function getSnippet()
    {
        return $this->snippet;
    }

    /**
     * @param mixed $snippet
     */
    public function setSnippet($snippet)
    {
        $this->snippet = $snippet;
    }

    /**
     * @param string $type
     * @return string
     */
    public function getThumbnail($type = null)
    {
        if (!$type || !in_array($type,array_keys(self::$thumbnailVariations))) {
            return $this->thumbnail;
        } else {
            $split = explode('.', $this->thumbnail);
            $dimensions = self::$thumbnailVariations[$type];
            return $split[0] . '-' . $dimensions["width"] . 'x' . $dimensions["height"] . '.' . $split[1];
        }
    }

    /**
     * @param mixed $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
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