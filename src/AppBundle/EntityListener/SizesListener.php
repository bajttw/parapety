<?php

namespace AppBundle\EntityListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Sizes;
use AppBundle\Entity\PriceListItems;
/**
 * Sizes Listener
 */
class SizesListener
{

    public function preUpdate(AppEntity $entity, LifecycleEventArgs $event):void
    {
        parent::preUpdate($entity, $event);
        $entityManager = $event->getObjectManager();
        if($entity->getSavedField('active') !== null){
            $entities = $entityManager->getRepository(PriceListItems::class)->updateEntities([ 'active' => $entity->getActive() ? 1 : 0 ], [
                'filters' => [
                    'size' => [
                        'name' => 'size',
                        'value' => $entity->getId()
                    ]
                ]
            ]);
        }
    }

}
