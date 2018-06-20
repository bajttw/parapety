<?php
namespace AppBundle\Helpers;


use Symfony\Component\DependencyInjection\ContainerInterface ;
use AppBundle\Utils\Utils;

class FilterHelper{

    private $predefined=[
        'active' => [
            'name' => 'active',
            'data' => [
                ['v' => '1', 'n' => 'aktywny'],
                ['v' => '0', 'n' => 'nieaktywny']
            ],
            'd' => [
                'widget' => 'multiselect'
            ],
            'attr' => [
                'multiple' => 'multiple'
            ]
        ]
    ];

    private $predefinedHidden=[
        'active' => [
            'name' => 'active',
            'value' => '1'
        ]
    ];

    private $sc;
    private $eh;
    private $sh;

    public function __construct(ContainerInterface $seviceContainer){
        $this->sc=$seviceContainer;
        $this->sh=$seviceContainer->get('helper.settings');
        $this->eh=$seviceContainer->get('helper.entity');
    }

    public function generateHiddenFilter($options):?array
    {
        if(is_string($options)){
            $options=Utils::deep_array_value($options, $this->predefinedHidden);
        }    
        if(!is_array($options)){
            return null;
        }
        return [
            'name' => $options['name'],
            'type' => 'hidden',
            'value' => $options['value'],
            'options' => Utils::deep_array_value('options', $options, [])
        ];
    }

    public function generateFilter($options):?array
    {
        if(is_string($options)){
            $options=Utils::deep_array_value($options, $this->predefined);
        }    
        if(!is_array($options)){
            return null;
        }
        return [
            'name' => $options['name'],
            'type' => Utils::deep_array_value('type', $options, 'select'),
            'attr' => Utils::deep_array_value('attr', $options, []),
            'd' => $this->getD($options),
            'data' => $this->getData($options)
        ];
    }

    private function getD(array $options):array
    {
        $d=Utils::deep_array_value('d', $options, []);
        if (array_key_exists('setValue' ,$options)) {
            $d['def-value'] = $this->sh->getSettingValue($this->options['setValue']['query']);
        }
        return $d;
    }

    private function getBannedValues(array $options):array
    {
        $banned=Utils::deep_array_value('banned', $options);
        if(is_string($banned)){
            $banned= $this->sh->getSettingValue($banned);
        }
        return is_array($banned) ? $banned : [];
    }

    private function getData(array $options):array
    {
        $data=Utils::deep_array_value('data', $options);
        if(is_array($data)){
            return $data;
        }
        $data=[];
        $source=$options['source'];
        switch($source['type']){
            case 'settings':
                $banned= $this->getBanned();
                $rows=$this->sh->getSettingValue($source['query']);
                if(is_array($rows)){
                    foreach($rows as $row){
                        if(is_array($banned) && count($banned) && in_array(array_key_exists('v', $row) ? $row['v'] : $row['value'] , $banned)){
                            continue;
                        }
                        $data[]=$row;
                    }
                }
            break;
            case 'entity':
                // $repository=$this->getEntityManager()->getRepository(Utils::deep_array_value('repository', $source, self::$bundlePath . Utils::deep_array_value('query', $source, '')));
                $data = $this->eh->getRepository($source['query'])->getFilter(Utils::deep_array_value('options', $source, [])) ;
            break;

        }  
        if(isset($options['add'])){
            $start=Utils::deep_array_value('add-start', $options);
            $end=Utils::deep_array_value('add-end', $options);
            if(is_array($start)){
                foreach($start as $row){
                    array_unshift($data, $row);
                }
            }
            if(is_array($end)){
                foreach($end as $row){
                    array_push($data, $row);
                }
            }
        }
        return $data;

    }

    public function generateFilters(string $type='index',  ?string $entityClassName=null, array $options=[]):array
    {
        $filters=[
            'visible' => [],
            'hidden' => []
        ];
        $entityClassName=$this->eh->getEntityClassName($entityClassName);
        $fn='get'.$entityClassName.'Filters';
        foreach($this->$fn($type, $options) as $name => $filter){
            $fname=Utils::deep_array_value('name', $filter, $name);
            if (Utils::deep_array_value('type', $filter) == 'hidden'){
                $filters['hidden'][$fname] = $this->generateHiddenFilter($filter);
            }else{
                $filters['visible'][$fname] = $this->generateFilter($filter);
            }
        }
        return $filters;
    }

    private function choiceFilters(array &$defined, array $names):array
    {
        $filters=[];
        foreach($names as $n){
            $filters[$n]= $defined[$n];
        }
        return $filters;
    }

    public function getOrdersFilters(string $type='index', array $options=[]):array
    {

    }

    public function getClientsFilters(string $type='index', array $options=[]):array
    {
        $defined= [
            'active' => 'active',
            'users' =>[
                'name' => 'users.id',
                'source' => [
                    'type' => 'entity',
                    'query' => 'Users',
                    'options' => [
                        'filters' => [
                            'type' => [
                                'condition' => 'gte',
                                'value' => '2'
                            ],
                            'enabled' => [ 'value' => true]
                        ]
                        
                    ]
                ],
                'attr' => [
                    'multiple' => 'multiple'
                ],
                'd' => [
                    'widget' => 'multiselect'                
                ]
            ],
            'regular' =>[
                'name' => 'regular',
                'data' => [
                    ['v' => '1', 'n' => 'stały'],
                    ['v' => '0', 'n' => 'zwykły']
                    ],
                    'd' => [
                        'widget' => 'multiselect'
                    ],
                    'attr' => [
                        'multiple' => 'multiple'
                    ]
            ]
        ];
        $defType='index';
        $filterTypes=[
            'index' => ['active', 'regular']
        ];
        return $this->choiceFilters($defined, array_key_exists($type, $filterTypes) ? $filterTypes[$type] : $filterTypes[$type]); 
    }



}