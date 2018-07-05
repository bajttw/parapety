<?php

namespace AppBundle\EntityListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Uploads;
use AppBundle\Helpers\FileUploader;
use Symfony\Component\DependencyInjection\ContainerInterface ;
/**
 * Uploads Listener
 */
class UploadsListener
{
    private $eh;
    private $fu;

    public function __construct(ContainerInterface $seviceContainer, FileUploader $fileUploader){
        $this->eh=$seviceContainer->get('helper.entity');
        $this->fu=$fileUploader;
    }

    public function prePersist(Uploads $upload, LifecycleEventArgs $args)
    {
        $this->moveFile($upload);
    }


    public function preUpdate(Uploads $upload, LifecycleEventArgs $event):void
    {
        $old=$upload->getOld();
        $oldname=$old->name;
        $name=$upload->getName();
        $folder=$upload->getFolder();
        if( isset($oldname) && $name != $oldname ){//file change
            $this->fu->remove($oldname, $folder);
        }
        $this->moveFile($upload);
        $kk=0;
    }

    public function postRemove(Uploads $upload, LifecycleEventArgs $event):void
    {
        $this->fu->remove($upload->getName(), $upload->getFolder());
    }

    private function moveFile(Uploads $upload)
    {
        $uploadFolder=$upload->getUploadFolder();
        $folder=$upload->getFolder();
        if($uploadFolder && $folder && ($folder != $uploadFolder)){
            $new=$this->fu->move($upload->getName(), $folder, $uploadFolder );
            if(is_array($new)){
                $upload->setUrl($new['url']);
            }
        }
    }
}
