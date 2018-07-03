<?php

namespace AppBundle\EntityListener;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Models;
use Symfony\Component\DependencyInjection\ContainerInterface ;
/**
 * Models Listener
 */
class ModelsListener
{
    private $eh;
    public function __construct(ContainerInterface $seviceContainer){
        $this->eh=$seviceContainer->get('helper.entity');
    }

    public function postUpdate(Models $model, LifecycleEventArgs $event):void
    {
        $kk=0;
    }
}