<?php

namespace AppBundle\EntityListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\DependencyInjection\ContainerInterface ;
use AppBundle\Entity\AppEntity;
/**
 * EntityWithChildsListener
 */
class EntityWithChildsListener
{
    protected $eh;

    public function __construct(ContainerInterface $seviceContainer){
        $this->eh=$seviceContainer->get('helper.entity');
    }

    protected function checkCollection($em, ?Collection $oldCollection, Collection $collection):void
    {
        if($oldCollection){
            foreach ($oldCollection as $old) {
                if (false === $collection->contains($old)) {
                    $em->remove($old);
                }
            }
        }
    } 

    protected function checkChildsCollections($em, AppEntity $entity, array $names):void
    {
        foreach($names as $name){
            $fnGet='get'.ucfirst($name);
            $this->checkCollection($em, $entity->getSavedField($name), $entity->$fnGet());
        }        
    }

    protected function clearCollection($em, Collection $collection):void
    {
        foreach ($collection as $old) {
            $em->remove($old);
        }
    }

    protected function clearChildsCollections($em, AppEntity $entity, array $names):void
    {
        foreach($names as $name){
            $fnGet='get'.ucfirst($name);
            $this->clearCollection($em, $entity->$fnGet());
        }        
    }
}
