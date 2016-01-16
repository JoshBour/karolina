<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 11/6/2014
 * Time: 10:11 μμ
 */

namespace Application\Service;


use Application\Entity\TempFile;
use Application\Validator\File;
use Application\Validator\Image;
use Zend\File\Transfer\Transfer;
use Zend\Filter\File\Rename;
use Zend\Filter\File\RenameUpload;
use Zend\Validator\File\Extension;

class FileUtilService extends BaseService
{
    private $tempFileRepository;

    public function upload($data)
    {
        $options = array(
            'maxSize' => '40960',
        );

        if ($data['type'] == "image") {
            $fileValidator = new Image($options);
        } else {
            $fileValidator = new File($options);
        }

        if ($fileValidator->isValid($data["uploadFile"])) {
            // we delete the previous image
            if (isset($data['previous']) && !empty($data['previous']))
                self::deleteFile(ROOT_PATH . '/' . $data['location'] . '/' . $data['previous']);

            // $fullName = $newName . '.' . self::getFileExtension($data['uploadFile']['type']);

            $fullName = self::rename($data['uploadFile'], $data['location'], $data['prefix']);

            return $fullName;
        } else {
            $this->message = $fileValidator->getMessages();
            return false;
        }
    }

    public static function resize($file, $location, $width, $height = null, $applyExtension = null)
    {
        $path = ROOT_PATH . '/public/' . $location . '/';
        $splitName = explode('.', $file);
        try {
            $img = new \abeautifulsite\SimpleImage($path . $file);
            // the format is: <path>/<filename>-<width>x<height>.<type>
            if (!$height)
                $img = $img->fit_to_width($width);
            else
                $img = $img->thumbnail($width, $height);

            $newName = $applyExtension ? $path . $splitName[0] . '.' . $applyExtension : $path . $splitName[0] . '-' . $width . 'x' . $height . '.' . $splitName[1];

            $img->save($newName);

            $mode = 644;
            if (!chmod($newName, octdec($mode))) return false;
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function rename($file, $location, $prefix, $name = null)
    {
        $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
        $newName = $name ? $name : uniqid($prefix . '-') . rand(1000000, 9999999) . "." . $extension;
        $loc = ROOT_PATH . '/public/' . $location . '/' . $newName;
        try {
            $rename = new RenameUpload(array(
                'target' => $loc,
                'overwrite' => true,
            ));
            $filter = $rename->filter($file);
            $mode = 644;
            if (!$filter || !chmod($loc, octdec($mode))) return false;
            return $newName;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function clearTempFiles()
    {
        $files = $this->getTempFileRepository()->findAll();
        $em = $this->getEntityManager();
        try {
            /**
             * @var \Application\Entity\TempFile $file
             */
            foreach ($files as $file) {
                self::deleteFile($file->getLocation());
                $em->remove($file);
            }
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getFileExtension($type)
    {
        $extension = '';
        switch ($type) {
            case 'image/gif':
                $extension = 'gif';
                break;
            case 'image/pjpeg':
            case 'image/jpeg':
                $extension = 'jpg';
                break;
            case 'image/png':
            case 'image/x-png':
                $extension = 'png';
                break;
        }
        return $extension;
    }

    public static function getFilePath($file, $subdir, $type)
    {
        $basePath = ROOT_PATH . '/' . $type . '/' . $subdir . '/' . $file;
        return file_exists($basePath) ? $basePath : false;

    }

    public static function deleteFile($file)
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function getTempFileRepository()
    {
        if (null == $this->tempFileRepository)
            $this->tempFileRepository = $this->getRepository('application', 'tempFile');
        return $this->tempFileRepository;
    }
} 