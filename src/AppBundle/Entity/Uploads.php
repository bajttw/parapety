<?php

namespace AppBundle\Entity;

//use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use AppBundle\Utils\Utils;

use AppBundle\Helpers\EntityHelper;
use AppBundle\Helpers\FileUploader;

// use AppBundle\Utils\UploadHandler as UploadHandler;

/**
 * Uploads
 */
class Uploads extends AppEntity
{
    const en = 'uploads';
    const ec = 'Uploads';
    const idPrototype = '__uid__';
    
    // public $types=['tmp', 'colors', 'models', 'trims', 'clients', 'orders', 'positions'];

// <editor-fold defaultstate="collapsed" desc="Fields utils">
    public static $dicNames = [
        'id' => 'id',
        'name' => 'img',
        'url' => 'url',
    ];

    public static $shortNames = [
        'id' => 'id',
        'name' => 'n',
        'original' => 'o',
        'type' => 't',
        'size' => 's',
        'folder' => 'p',
        'url' => 'url'
    ];

    public static function getFields(?string $type = null):array
    {
        switch ($type) {
            case '':
                $fields = diff_array(parent::getFields($type));
                break;
            default:
                $fields = ['id', 'name', 'url'];
        }
        return $fields;
    }

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Variables">
    private $id;
    private $name;
    private $original;
    private $type;
    private $size;
    private $folder;
    private $uploadFolder;
    private $url;
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Variables extra">
    // private $old = array('name' => '', 'file' => '', 'thumbnail' => '');
// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Data functions">
    public function getFileUrl()
    {
        return $this->getUrl().'/'. $this->getName();
    }

    public function __toString():string
    {
        return $this->getFileUrl();
    }

    public function getData(array $options=[]):array
    {
        $data = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'original' => $this->getOriginal(),
            'size' => $this->getSizeStr(),
            'type' => $this->getType(),
            'folder' => $this->getFolder(),
            'url' => $this->getUrl()
        ];
        return $data;
    }

    public function setData(?array $data=null)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $fnSet = 'set' . ucfirst($key);
                if (method_exists($this, $fnSet)) {
                    $this->$fnSet($value);
                }
            }
        }
        return $this;
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Fields functions">
    public function getId():?int
    {
        return $this->id;
    }

    public function setName(string $name)
    {
        $this->saveFieldValue('name');
        $this->name = $name;
        return $this;
    }

    public function getName():?string
    {
        return $this->name;
    }

    public function setOriginal(string $original)
    {
        $this->original = $original;
        return $this;
    }

    public function getOriginal():?string
    {
        return $this->original;
    }

    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    public function getType():string
    {
        return $this->type;
    }


    public function setFolder(?string $folder)
    {
        if($folder){
            $this->folder = $folder;
        }
        return $this;
    }

    public function getFolder():?string
    {
        return $this->folder;
    }

    public function setUploadFolder(?string $uploadFolder)
    {
        $this->uploadFolder = $uploadFolder;
        return $this;
    }

    public function getUploadFolder():?string
    {
        return $this->uploadFolder;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }
    
    public function getSize():?int
    {
        return $this->size;
    }

    public function getSizeStr()
    {
        $units=['B', 'kB', 'MB', 'GB'];
        $calc=$this->size;
        $d=1024;
        $i=0;
        while($i < 4 && $calc > $d){
            $calc= $calc/$d;
            $i++;
        }
        return sprintf('%.2f'.$units[$i], $calc );
    }


// </editor-fold>

// public function changeUploadType($newUploadType)
    // {
    //     if ($this->uploadType != $newUploadType) {
    //         $this->moveFile(self::$types[$this->uploadType], self::$types[$newUploadType]);
    //         $this->uploadType = $newUploadType;
    //     }
    // }

    // public function setFile(array $uploaded)
    // {
    //     $this->setName($uploaded['name']);
    //     $this->setOriginal($uploaded['original']);
    //     $this->setSize($uploaded['size']);
    //     $this->setType($uploaded['type']);
    //     $this->setPath($uploaded['path']);
    //     $this->setUrl($uploaded['fullUrl']);
    //     return $this;
    // }

    // public function setFile($uploadedFile)
    // {
    //     $this->setPath($uploadedFile->path);
    //     $this->url = str_replace($uploadedFile->name, "", $uploadedFile->url);
    //     $this->original = $uploadedFile->original;
    //     $this->name = $uploadedFile->name;
    //     $this->size = $uploadedFile->size;
    //     $this->type = $uploadedFile->type;
    // }

    // public function getFilePath($subdir = '')
    // {
    //     if ($this->name != null) {
    //         $filepath = $this->getPath() . ($subdir == '' ? '' : $subdir . DIRECTORY_SEPARATOR) . $this->getName();
    //         return file_exists($filepath) ? $filepath : null;
    //     }
    //     return null;
    // }

    // public function getFilePath(string $subdir = ''):?string
    // {
    //     if ($this->name != null) {
    //         $filepath = $this->getPath() . ($subdir == '' ? '' : $subdir . DIRECTORY_SEPARATOR) . $this->getName();
    //         return file_exists($filepath) ? $filepath : null;
    //     }
    //     return null;
    // }

    // public function getFileUrl($subdir = '')
    // {
    //     return $this->getFullUrl() . ($subdir == '' ? '' : $subdir . DIRECTORY_SEPARATOR) . $this->getName();
    // }
    // public function getThumbnailPath()
    // {
    //     return $this->getFilePath('thumbnail');
    // }
    // public function getThumbnailUrl()
    // {
    //     return $this->getFileUrl('thumbnail');
    // }

    // private function moveFile($oldFolder, $newFolder)
    // {
    //     if ($filepath = $this->getFilePath()) {
    //         $newPath = str_replace($oldFolder, $newFolder, $this->path);
    //         $file = new File($filepath);
    //         $file->move($newPath);
    //         if ($thumbpath = $this->getThumbnailPath()) {
    //             $thumb = new File($thumbpath);
    //             $thumb->move($newPath . 'thumbnail' . DIRECTORY_SEPARATOR);
    //         }
    //         $this->path = $newPath;
    //         $this->url = str_replace($oldFolder, $newFolder, $this->url);
    //         $this->url = str_replace($this->name, "", $this->url);
    //     }
    // }

    // public function removeUpload()
    // {
    //     if ($file = $this->getFilePath()) {
    //         unlink($file);
    //     }
    //     // if ($thumbnail = $this->getThumbnailPath()) {
    //     //     unlink($thumbnail);
    //     // }
    // }

}
