<?php
namespace AppBundle\Helpers;
use AppBundle\Utils\Utils;
// use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
class ToolsGenerator extends ActionsGenerator
{

    protected $genType='tools';

    protected $predefined=[
        'new' => [ 
            'action' => 'new', 
            'attr' => [ 'class' => 'btn-primary' ]
        ]
    ];

    protected function generateAction(array $actionOptions):array
    {
        $action = parent::generateAction($actionOptions);
        $action['type']=Utils::deep_array_value('type', $actionOptions, 'btn');
        return $action;
    }

    protected function getGenericElements():array
    {
        return [
            'predefined' => [               
                'new' => 'new'
            ],
            'types'=>[
                'index' => ['new'],
                'def' => ['new']
            ]
        ];
    }

}