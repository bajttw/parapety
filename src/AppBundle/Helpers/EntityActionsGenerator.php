<?php
namespace AppBundle\Helpers;
use AppBundle\Utils\Utils;
// use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
class EntityActionsGenerator extends ActionsGenerator
{

    protected $genType='eactions';

    private $entityId;

    protected $predefined=[
        'edit' => [ 
            'action' => 'edit'
        ],
        'show' => [ 
            'action' => 'show',
        ],
        'delete' => [ 
            'action' => 'delete',
            'renderType' => 'm'
        ]
    ];

    protected function init(?string $type=null,  ?string $entityClassName=null, array $options=[]):void
    {
        parent::init($type, $entityClassName, $options);
        $this->entityId=Utils::deep_array_value('entityId', $options, $this->eh->getIdPrototype() );
    }

    protected function getUrl(string $name, array $parameters=[]):string
    {
        $parameters['id'] = $this->eh->getIdPrototype();
        return parent::getUrl($name, $parameters);
    }

    protected function getGenericElements():array
    {
        return [
            'predefined' => [               
                'edit' => 'edit',
                'delete' => 'delete'
            ],
            'types'=>[
                'index' => ['edit', 'delete'],
                'def' => ['edit', 'delete']
            ]
        ];
    }

}