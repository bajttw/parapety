<?php

namespace AppBundle\EntityListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\AppEntity;
use AppBundle\Entity\Colors;
use AppBundle\Entity\PriceListItems;
/**
 * Colors Listener
 */
class ColorsListener extends EntityWithUploadListener
{

    public function preUpdate(AppEntity $entity, LifecycleEventArgs $event):void
    {
        parent::preUpdate($entity, $event);
        $entityManager = $event->getObjectManager();
        if($entity->getSavedField('active') !== null){
            $entities = $entityManager->getRepository(PriceListItems::class)->updateEntities([ 'active' => $entity->getActive() ? 1 : 0 ], [
                'filters' => [
                    'color' => [
                        'name' => 'color',
                        'value' => $entity->getId()
                    ]
                ]
            ]);
        }
    }

}
