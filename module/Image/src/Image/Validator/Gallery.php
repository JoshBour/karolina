<?php
namespace Image\Validator;

use Zend\Validator\AbstractValidator;

class Gallery extends AbstractValidator
{
    const DUPLICATE_IMAGE = "duplicateAttribute";
    const EMPTY_IMAGES = 'emptyImages';
    const EMPTY_POSITION = 'emptyPosition';
    const EMPTY_TITLE = 'emptyTitle';
    const INVALID_POSITION = 'invalidPosition';
    const INVALID_IMAGES = 'invalidImages';
    const POSITION_NAN = 'positionNan';

    protected $messageTemplates = array(
        self::EMPTY_IMAGES => "The images can't be empty.",
        self::INVALID_POSITION => "The image position must be a valid integer between 1-999.",
        self::POSITION_NAN => "The image position must be an integer.",
        self::EMPTY_POSITION => "Some images have empty positions, please check them and try again.",
        self::EMPTY_TITLE => "Some images have empty titles, please check them and try again.",
        self::INVALID_IMAGES => "Something went wrong when decoding the images.",
        self::DUPLICATE_IMAGE => "You have included duplicate images."
    );

    protected $validators;

    protected $filters;

    public function __construct($options = array())
    {
        parent::__construct($options);
    }

    public function isValid($images)
    {
        if(!is_array($images)) $images = json_decode($images,true);


        if ($images === null) {
            $this->error(self::INVALID_IMAGES);
            return false;
        }

        if (empty($images)) {
            $this->error(self::EMPTY_IMAGES);
            return false;
        }

        $error = false;
        $included = [];
        foreach ($images as $image) {
            if(in_array($image['joinId'],$included)){
                $this->error(self::DUPLICATE_IMAGE, $this->messageTemplates[self::DUPLICATE_IMAGE]);
                $error = true;
                continue;
            }else {
                $included[] = $image['joinId'];
            }

            // we don't return false in this one so that we can check the position as well
            if (empty($image['title'])) {
                $this->error(self::EMPTY_TITLE, $this->messageTemplates[self::EMPTY_TITLE]);
                $error = true;
            }

            if (empty($image['position'])) {
                $this->error(self::EMPTY_POSITION, $this->messageTemplates[self::EMPTY_POSITION]);
                $error = true;
                continue;
            }

            if (!is_int((int)$image['position'])) {
                $this->error(self::POSITION_NAN, $this->messageTemplates[self::POSITION_NAN]);
                $error = true;
                continue;
            }

            if ($image['position'] < 1 || $image['position'] > 999) {
                $this->error(self::INVALID_POSITION, $this->messageTemplates[self::INVALID_POSITION]);
                $error = true;
                continue;
            }
        }

        return !$error;
    }

}