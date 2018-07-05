<?php

namespace AppBundle\EntityListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\AppEntity;
use AppBundle\Helpers\FileUploader;
use Symfony\Component\DependencyInjection\ContainerInterface ;
/**
 * EntityWithChildsListener
 */
class EntityWithChildsListener
{
    protected $eh;

    public function __construct(ContainerInterface $seviceContainer){
        $this->eh=$seviceContainer->get('helper.entity');
    }

    public function preUpdate(AppEntity $entity, LifecycleEventArgs $event):void
    {
        $entityManager = $event->getObjectManager();
    }

}
