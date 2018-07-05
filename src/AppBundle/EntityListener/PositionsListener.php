<?php

namespace AppBundle\EntityListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface ;
use AppBundle\Entity\AppEntity;
use AppBundle\Entity\Positions;
/**
 * Positions Listener
 */
class PositionsListener extends EntityWithChildsListener
{

    public function postPersist(AppEntity $entity, LifecycleEventArgs $event):void
    {
    }

    public function preUpdate(AppEntity $entity, LifecycleEventArgs $event):void
    {
        $em = $event->getObjectManager();
        // $this->checkChildsCollections($em, $entity, [ 'uploads']);
    }

    public function preRemove(AppEntity $entity, LifecycleEventArgs $event):void
    {
        $em = $event->getObjectManager();
        // $this->clearChildsCollections($em, $entity, [ 'uploads']);
    }

}