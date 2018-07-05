<?php

namespace AppBundle\Helpers;
use AppBundle\Helpers\EntityHelper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Router;

class FileUploader
{
    private $appDirectory;
    private $uploadsDirectory;
    private $eh;
    private $rc;
    private $tmp='tmp';
    
    private function getTargetDirectory(string $folder):string
    {
        return $this->getUploadsDirectory(). '/' . $folder;
    }

    private function getFolder(?string $folder=null):string
    {
        return $folder ?? $this->tmp;
    }

    public function __construct(Router $router, EntityHelper $entityHelper ,string $projectDirectory, string $uploadsDirectory)
    {
        $this->projectDirectory = $projectDirectory;
        $this->uploadsDirectory = $uploadsDirectory;
        $this->eh=$entityHelper;
        $this->rc=$router->getContext();
    }

    public function upload(UploadedFile $file, ?string $folder=null):array
    {
        $folder= $this->getFolder($folder);
        $name = $this->validateName(str_replace('.' . $file->getClientOriginalExtension(), '', $file->getClientOriginalName() ));
        $dir = $this->getTargetDirectory($folder);
        $filename = md5(uniqid().$name).'.'.$file->guessExtension();
        $file->move($dir, $filename);
        return [
            'name' => $filename,
            'original' => $file->getClientOriginalName(),
            'size' => $file->getClientSize(),
            'type' => $file->getClientMimeType(),
            'uploadFolder' => $folder, 
            'url' => $this->getUrl($folder)
        ];
    }

    public function getUrl(?string $folder=null):string
    {       
        return $this->rc->getBaseUrl().'/'.$this->uploadsDirectory.'/'.$this->getFolder($folder).'/' ;
    }

    public function getUploadsDirectory():string
    {
        return $this->projectDirectory.'/web/'.$this->uploadsDirectory ;
    }
   
    public function validateName(string $filename):string
    {
        return substr(\preg_replace("/[^a-zA-Z0-9_]/", "_", $filename), 0, 25);
    }

    public function move(string $name, string $newFolder, ?string $oldFolder=null ):?array
    {
        $op=$this->getTargetDirectory($this->getFolder($oldFolder));
        $np=$this->getTargetDirectory($newFolder);
        $file = new File($op.'/'.$name);
        if($file){
            $file->move($np);
            return([
                'path' => $newFolder,
                'url' => $this->getUrl($newFolder)
            ]);
        }
        return null;
    }

    public function remove(string $name, ?string $folder=null):bool
    {
        $fp=$this->getTargetDirectory($this->getFolder($folder)).'/'.$name ;
        if(file_exists($fp)){
            unlink($fp);
        }
        return false;
    }
}