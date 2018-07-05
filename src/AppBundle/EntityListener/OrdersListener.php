<?php

namespace AppBundle\EntityListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface ;
use AppBundle\Entity\AppEntity;
use AppBundle\Entity\Orders;
/**
 * Orders Listener
 */
class OrdersListener extends EntityWithChildsListener
{

    public function prePersist(AppEntity $order, LifecycleEventArgs $event):void
    {
        $order->genNumber($this->eh->getNumerate($order::ec, $order->getClient()->getId()));
    }

    public function postPersist(AppEntity $order, LifecycleEventArgs $event):void
    {
        $this->eh->saveEntityNumber($order::ec, $order->getIntNr()+1, $order->getClient()->getId());
    }

    public function preUpdate(AppEntity $entity, LifecycleEventArgs $event):void
    {
        $em = $event->getObjectManager();
        // $this->checkChildsCollections($em, $entity, ['positions', 'uploads', 'notes']);
    }

    public function preRemove(AppEntity $entity, LifecycleEventArgs $event):void
    {
        $em = $event->getObjectManager();
        // $this->clearChildsCollections($em, $entity, ['positions', 'uploads', 'notes']);
    }

}