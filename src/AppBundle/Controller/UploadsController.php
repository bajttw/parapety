<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Uploads;
use AppBundle\Controller\AppController;
use AppBundle\Helpers\FileUploader;
// use AppBundle\Utils\UploadHandler as UploadHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UploadsController extends AppController{
    const en='uploads';
    const ec='Uploads';
    
    public function uploadTempAction(Request $request, FileUploader $fileUploader){
        if (count($request->files->keys())>0){
            $paramName=$request->files->keys()[0];
            $uploaded=$request->files->get($paramName);
            $files=[];
            $ok=true;
            $uploads=[];    
            foreach ($uploaded as $key => $file){
                if (isset($file->error)){
                    $files[$key]= \json_encode(["error" => $file->error]);
                    $ok=false;
                }else{
                    $files[$key]=$fileUploader->upload($file);
                }
            }
            return $this->JsonResponse(['files'=> $files], $ok);//!isset($uploaded["files"][0]->error));                
        }
        return $this->JsonResponse(['files'=> []], false);//!isset($uploaded["files"][0]->error));                
    }

        
}
