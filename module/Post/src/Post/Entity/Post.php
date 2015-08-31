<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:27 πμ
 */

namespace Post\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class post
 * @package post\Entity
 * @ORM\Entity(repositoryClass="Post\Repository\PostRepository")
 * @ORM\Table(name="posts")
 */
class Post {

    public static $thumbnailVariations = array(
        "list" => array("width" => 400, "height" => 235),
        "admin" => array("width" => 100, "height" => 70));

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=5, name="post_id")
     */
    private $postId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $thumbnail;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", name="post_date")
     */
    private $postDate;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $url;

    public function encodeUrl(){
        $words = array_slice(str_word_count($this->title,1),0,10);
        foreach($words as $key => $word){
            $words[$key] = strtolower($word);
        }
        return join('-',$words);
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $postDate
     */
    public function setPostDate($postDate)
    {
        if(!$postDate instanceof \DateTime)
            $postDate = new \DateTime($postDate);
        $this->postDate = $postDate;
    }

    /**
     * @return mixed
     */
    public function getPostDate()
    {
        return $this->postDate;
    }

    /**
     * @param mixed $postId
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;
    }

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param mixed $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
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
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }


} 