<?php

namespace AppBundle\EntityListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Utils\Utils;
use AppBundle\Entity\Productions;
use Symfony\Component\DependencyInjection\ContainerInterface ;
/**
 * Productions Listener
 */
class ProductionsListener
{
    private $eh;
    public function __construct(ContainerInterface $seviceContainer){
        $this->eh=$seviceContainer->get('helper.entity');
        $tesr=99;
    }
    public function prePersist(Productions $production, LifecycleEventArgs $event):void
    {
        $order->genNumber($this->eh->getEntityNumerate($production::ec));
    }

    public function postPersist(Productions $production, LifecycleEventArgs $event):void
    {
        $this->eh->saveEntityNumber($production::ec, $production->getIntNr()+1);
    }
}