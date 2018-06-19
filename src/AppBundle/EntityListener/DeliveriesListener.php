<?php

namespace AppBundle\EntityListener;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Deliveries;
use Symfony\Component\DependencyInjection\ContainerInterface ;
/**
 * Deliveries Listener
 */
class DeliveriesListener
{
    private $eh;
    public function __construct(ContainerInterface $seviceContainer){
        $this->eh=$seviceContainer->get('helper.entity');
    }

    public function prePersist(Deliveries $delivery, LifecycleEventArgs $event):void
    {
        $delivery->genNumber($this->eh->getEntityNumerate($delivery::ec, $delivery->getClient()->getId()));
    }

    public function postPersist(Deliveries $delivery, LifecycleEventArgs $event):void
    {
        $this->eh->saveEntityNumber($delivery::ec, $delivery->getIntNr()+1, $delivery->getClient()->getId());
    }
}