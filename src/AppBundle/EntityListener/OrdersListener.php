<?php

namespace AppBundle\EntityListener;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Orders;
use Symfony\Component\DependencyInjection\ContainerInterface ;
/**
 * Orders Listener
 */
class OrdersListener
{
    private $eh;
    public function __construct(ContainerInterface $seviceContainer)
    {
        $this->eh=$seviceContainer->get('helper.entity');
    }

    public function prePersist(Orders $order, LifecycleEventArgs $event):void
    {
        $order->genNumber($this->eh->getNumerate($order::ec, $order->getClient()->getId()));
    }

    public function postPersist(Orders $order, LifecycleEventArgs $event):void
    {
        $this->eh->saveNumber($order::ec, $order->getIntNr()+1, $order->getClient()->getId());
    }
}