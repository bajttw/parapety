<?php
namespace AppBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;

class EntityToHiddenTransformer implements DataTransformerInterface
{
    protected $objectManager;
    private $entityClass;
    private $options = [];

    public function __construct(ObjectManager $objectManager, $entityClass, $options = [])
    {
        $this->objectManager = $objectManager;
        $this->entityClass = $entityClass;
        $this->options = array_replace(['shortNames' => 'dic' ], $options);
    }

    public function transform($entity)
    {
        if (null === $entity) {
            return;
        }
        return $entity->getDataJSON($this->options);
    }

    public function reverseTransform($data)
    {
        $entityData = json_decode($data, true);
        $entityClass = $this->entityClass;
        $entity = null;
        if ($entityData != null) {
            $id = $entityData['id'] ?? $entityData['v'] ?? null;
            $entity = is_null($id) ? new $entityClass()  
                :$this->objectManager->getRepository($entityClass)->find($id);
            $entity->setData($entityData);
        }
        return $entity;
    }
}
