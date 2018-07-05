<?php

namespace AppBundle\EntityListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\AppEntity;
use AppBundle\Helpers\FileUploader;
use Symfony\Component\DependencyInjection\ContainerInterface ;
/**
 * EntityWithUploadListener
 */
class EntityWithUploadListener
{
    protected $eh;
    protected $fuh;

    public function __construct(ContainerInterface $seviceContainer, FileUploader $fileUploader){
        $this->eh=$seviceContainer->get('helper.entity');
        $this->fuh=$fileUploader;
    }

    public function prePersist(AppEntity $entity, LifecycleEventArgs $args)
    {
        $upload=$entity->getUpload();
    }

    public function preUpdate(AppEntity $entity, LifecycleEventArgs $event):void
    {
        $entityManager = $event->getObjectManager();
        $oldUpload=$entity->getSavedField('upload');
        if($oldUpload && $entity->getUpload() != $oldUpload ){
            $entityManager->remove($oldUpload);
        }
    }

}
