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
        ],
        'client' => [
            'name' => 'client',
            'source' => [
                'type' => 'entity',
                'query' => 'Clients',
            ],
            'attr' => [
                'multiple' => 'multiple'
            ],
            'd' => [
                'widget' => 'multiselect'                
            ]
        ]
    ];

    private $predefinedHidden=[
        'active' => [
            'name' => 'active',
            'value' => '1'
        ],
        'client' => [
            'name' => 'client',
            'source' => [
                'type' => 'options',
                'query' => 'client'
            ]
        ]
    ];

    private $sc;//serviceContainer
    private $eh;//entityHelper
    private $sh;//settingsHelper
    private $en;//entityName
    private $ecn;//entityClassName
    private $options=[];

    public function __construct(ContainerInterface $seviceContainer){
        $this->sc=$seviceContainer;
        $this->sh=$seviceContainer->get('helper.settings');
        $this->eh=$seviceContainer->get('helper.entity');
    }

    private function getFilter($filter):?array
    {
        if(is_string($filter)){
            $f=split('-', $filter);
            if($f[0]=='hidden'){
                if(array_key_exists($f[1], $this->predefinedHidden)){
                    $filter=$this->predefinedHidden[$f[1]];
                    $filter['type']='hidden';
                }else{
                    $filter= null;
                }
            }else{
                $filter=array_key_exists($f[1], $this->predefined) ? $this->predefined[$f[1]] : null;
            }
        }
        return $filter;
    }

    private function getValue(array &$filterOptions)
    {
        if(array_key_exists('value', $filterOptions)){
            return $filterOptions['value'];
        }
        $value=null;
        $source=$filterOptions['source'];
        switch($source['type']){
            case 'settings':
                $value=$this->sh->getSettingValue($source['query']);
            break ;
            case 'options':
                $value=Utils::deep_array_value('values-' . $source['query'], $this->options);
            break;
        }
        return $value;
    }

    private function getData(array &$filterOptions):array
    {
        $data=Utils::deep_array_value('data', $filterOptions);
        if(is_array($data)){
            return $data;
        }
        $data=[];
        $source=$filterOptions['source'];
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
        if(isset($filterOptions['add'])){
            $start=Utils::deep_array_value('add-start', $filterOptions);
            $end=Utils::deep_array_value('add-end', $filterOptions);
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

    private function getBannedValues(array &$filterOptions):array
    {
        $banned=Utils::deep_array_value('banned', $filterOptions);
        if(is_string($banned)){
            $banned= $this->sh->getSettingValue($banned);
        }
        return is_array($banned) ? $banned : [];
    }

    private function getD(array $filterOptions):array
    {
        $d=Utils::deep_array_value('d', $filterOptions, []);
        if (array_key_exists('setValue' ,$filterOptions)) {
            $d['def-value'] = $this->sh->getSettingValue($this->options['setValue']['query']);
        }
        return $d;
    }

    private function choiceFilters(array &$defined, array $names):array
    {
        $filters=[];
        foreach($names as $n){
            $filters[$n]= $defined[$n];
        }
        return $filters;
    }

    private function getEntityFilters(string $type='index')
    {
        $getFunction='get'. $this->ecn. 'Filters';
        return $this->$getFunction($type);
    }

    private function generateHiddenFilter($filterOptions):?array
    {
        if(is_string($filterOptions)){
            $filterOptions=Utils::deep_array_value($filterOptions, $this->predefinedHidden);
        }    
        if(!is_array($filterOptions)){
            return null;
        }
        return [
            'name' => $filterOptions['name'],
            'type' => 'hidden',
            'value' => $this->getValue($filterOptions),
            'options' => Utils::deep_array_value('options', $filterOptions, [])
        ];
    }

    public function generateFilter($filterOptions):?array
    {
        if(is_string($filterOptions)){
            $filterOptions=Utils::deep_array_value($filterOptions, $this->predefined);
        }    
        if(!is_array($filterOptions)){
            return null;
        }
        return [
            'name' => $filterOptions['name'],
            'type' => Utils::deep_array_value('type', $filterOptions, 'select'),
            'attr' => Utils::deep_array_value('attr', $filterOptions, []),
            'd' => $this->getD($filterOptions),
            'data' => $this->getData($filterOptions)
        ];
    }

    public function generateFilters(string $type='index',  ?string $entityClassName=null, array $options=[]):array
    {
        $this->options=$options;
        $this->ecn=$this->eh->getEntityClassName($entityClassName);
        $this->en=$this->eh->getEntityName($entityClassName);
        $filters=[
            'visible' => [],
            'hidden' => []
        ];
        foreach($this->getEntityFilters($type) as $name => $filter){
            $filter=$this->getFilter($filter);
            $fname=Utils::deep_array_value('name', $filter, $name);
            if (Utils::deep_array_value('type', $filter) == 'hidden'){
                $filters['hidden'][$fname] = $this->generateHiddenFilter($filter);
            }else{
                $filters['visible'][$fname] = $this->generateFilter($filter);
            }
        }
        return $filters;
    }

    public function getOrdersFilters(string $type='index'):array
    {
        $id = Utils::deep_array_value('id', $this->options);
        $cid = Utils::deep_array_value('cid', $this->options);
        $isClient= !is_null($cid);
        $defined = [
            'client' => 'client',
            'status' => [
                'name' => 'status',
                'source' => [
                    'type' => 'settings',
                    'query' => 'orders-dictionaries-status'
                ],
                'attr' => [
                    'multiple' => 'multiple'
                ],
                'd' => [
                    'widget' => 'multiselect'                
                ]
            ],
            'express' => [
                'name' => 'express',
                'source' => [
                    'type' => 'settings',
                    'query' => 'orders-dictionaries-express'
                ],
                'attr' => [
                    'multiple' => 'multiple'
                ],
                'd' => [
                    'widget' => 'multiselect'
                ]
            ],
            'created' => [
                'name' => 'created',
                'type' => 'input',
                'setValue' => [
                    'type' => 'settings',
                    'query' => 'orders-filters-created-value'
                ],
                'source' => [
                    'type' => 'settings',
                    'query' => 'orders-filters-dateRanges'
                ],
                // 'attr' => [
                //     'data-daterange' => json_encode([]),
                //     'data-filter-options' => json_encode(['type' => 'date_period']),
                // ],
                'd' => [
                    'filter-options' => json_encode(['type' => 'date_period']),
                    'widget' => 'daterange'
                ]
                
            ],
            'approved' => [
                'name' => 'approved',
                'type' => 'input',
                'setValue' => [
                    'type' => 'settings',
                    'query' => 'orders-filters-approved-value'
                ],
                'source' => [
                    'type' => 'settings',
                    'query' => 'orders-filters-dateRanges'
                ],
                'd' => [
                    'filter-options' => '{"type":"date_period"}', //json_encode(['type' => 'date_period']),
                    'widget' => 'daterange'
                ]
                
            ],
            'ways' => [
                'name' => 'client.ways.id',
                'label' => 'orders.label.filter.ways',
                'source' => [
                    'type' => 'entity',
                    'query' => 'Ways',
                ],
                'attr' => [
                    'multiple' => 'multiple'
                ],
                'd' => [
                    'widget' => 'multiselect'
                ]
            ],
            'not_package' => [
                'name' => 'positions.package',
                'type' => 'hidden',
                'value' => $id == null ? null : [null, $id],
            ],
            'package' => [
                'name' => 'positions.package',
                'type' => 'hidden',
                'value' => null,
                'options' => ['not' => true]
            ],
            'not_production' => [
                'name' => 'production',
                'type' => 'hidden',
                'value' => $id == null ? null : [null, $id],
            ],
            'production' => [
                'name' => 'production',
                'type' => 'hidden',
                'value' => null,
                'options' => ['not' => true]
            ],
            'not_shipment' => [
                'name' => 'shipment',
                'type' => 'hidden',
                'value' => $id == null ? null : [null, $id],
            ],
            'shipment' => [
                'name' => 'shipment',
                'type' => 'hidden',
                'value' => null,
                'options' => ['not' => true]
            ],
            'notState' => [
                'name' => 'status',
                'type' => 'hidden',
                'value' =>  Utils::deep_array_value('bannedStates', $options),
                'options' => ['not' => true]
            ],
            'posSize' => [
                'name' => 'positions.size',
                'type' => 'hidden',
                'source' => [
                    'type' => 'options',
                    'query' => 'size'
                ]
            ],
            'posPackage' => [
                'operator' => 'or',
                'name' => "positions.package",
                'type' => 'hidden',
                'source' => [
                    'type' => 'options',
                    'query' => 'package'
                ]
            ],
            'posNoPackage' => [
                'name' => "positions.package",
                'type' => 'hidden',
                'value' => null
            ]
        ];
        $filterTypes=[
            'client_index' => ['status', 'express', 'created'],
            'def' => ['status', 'express', 'created', 'approved'],
            'index' => ['client', 'status', 'express', 'created', 'approved'],
            'package' => ['ways', 'approved', 'status', 'express' ],
            'packageHiddens' => ['posNoPackage'],
            'production' => ['generated'],
            'productions_form' => ['client', 'approved', 'express'],
            'service' => ['status', 'express', 'created'],
            'shipment' => ['generated', 'production', 'not_shipment'],
            'table_client' => ['client_hidden'],
        ];
        $idx=array_key_exists($type, $filterTypes) ? $type : 'def';
        $filters=$this->choiceFilters($defined, $filterTypes[$idx]); 
        switch($idx){//custumize filters
            case 'package':
                $filters['status']['banned']='orders-filters-status-banned-package';
            break;
            case 'packageHiddens' :
                self::addFilter($filters, $fs['posNoPackage'], 'posNoPackage');
                if ($fs['notState']['value']) {
                    self::addFilter($filters, $fs['notState'], 'notState');
                }
                if ($fs['posSize']['value']) {
                    self::addFilter($filters, $fs['posSize'], 'posSize');
                }
                if ($fs['posPackage']['value']) {

                    self::addFilter($filters, $fs['posPackage'], 'posPackage');
                }
            break;
        }
        return $filters;
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
        $filterTypes=[
            'index' => ['active', 'regular']
        ];
        $idx=array_key_exists($type, $filterTypes) ? $type : 'index';
        return $this->choiceFilters($defined, $filterTypes[$idx]); 
    }



}