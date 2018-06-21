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
        ],
        'hidden' => [
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
        ]

    ];

    private $eh;//entityHelper
    private $sh;//settingsHelper

    private $en;//entityName
    private $ecn;//entityClassName
    private $options=[];

    public function __construct(EntityHelper $entityHelper, SettingsHelper $settingsHelper){
        $this->eh=$entityHelper;
        $this->sh=$settingsHelper;
    }

    private function getDefined(string $name, array &$definedFilters)
    {
        $filter=Utils::deep_array_value($name, $definedFilters);
        $gg=strpos($name, 'hidden-');
        if(is_array($filter) && strpos($name, 'hidden-') === 0 ){
            $filter['type']='hidden';
        }
        return $filter;
    }

    private function getFilter($filter):?array
    {
        if(is_string($filter)){
            return $this->getDefined($filter,$this->predefined);
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
                $value=Utils::deep_array_value(['values' , $source['query']], $this->options);
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
                $banned= $this->getBannedValues($filterOptions);
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

    private function getD(array &$filterOptions):array
    {
        $d=Utils::deep_array_value('d', $filterOptions, []);
        if (array_key_exists('setValue' ,$filterOptions)) {
            $d['def-value'] = $this->sh->getSettingValue($filterOptions['setValue']['query']);
        }
        return $d;
    }

    private function choiceFilters(array $names, array &$defined):array
    {
        $filters=[];
        foreach($names as $n){
            $filter=$this->getDefined($n, $defined);
            $filters[$n]= ($filter) ?: $n;
        }
        return $filters;
    }

    private function getEntityFilters(string $type='index'):array
    {
        $getFunction='get'. $this->ecn. 'Filters';       
        return method_exists($this, $getFunction) ? $this->$getFunction($type) : $this->getGenericFilters($type);
    }

    private function generateHiddenFilter(array &$filterOptions):array
    {
        return [
            'name' => $filterOptions['name'],
            'type' => 'hidden',
            'value' => $this->getValue($filterOptions),
            'options' => Utils::deep_array_value('options', $filterOptions, [])
        ];
    }

    public function generateFilter(array &$filterOptions):array
    {
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
            $fname= strpos($name, 'hidden-') === false ? $name : substr($name, 7) ;
            if (Utils::deep_array_value('type', $filter) == 'hidden'){
                $filters['hidden'][$fname] = $this->generateHiddenFilter($filter);
            }else{
                $filters['visible'][$fname] = $this->generateFilter($filter);
            }
        }
        return $filters;
    }

    private function getGenericFilters(string $type='index'):array
    {
        $defined= [
        ];
        $filterTypes=[
            'index' => ['active']
        ];
        $idx=array_key_exists($type, $filterTypes) ? $type : 'index';
        return $this->choiceFilters($filterTypes[$idx], $defined); 
    }

    public function getClientsFilters(string $type='index'):array
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
        return $this->choiceFilters($filterTypes[$idx], $defined); 
    }

    public function getOrdersFilters(string $type='index'):array
    {
        $pid = Utils::deep_array_value('values-id', $this->options);
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
                'd' => [
                    'filter-options' => '{"type" : "date_period"}',
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
                    'filter-options' => '{"type":"date_period"}',
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
            'hidden' => [
                'toProduction' => [
                    'name' => 'production',
                    'value' => is_null($pid) ? null : [null, $pid],
                ],
                'production' => [
                    'name' => 'production',
                    'value' => null,
                    'options' => ['not' => true]
                ],
                'notState' => [
                    'name' => 'status',
                    'type' => 'hidden',
                    'source' => [
                        'type' => 'options',
                        'query' => 'bannedStates'
                    ],
                    'options' => ['not' => true]
                ],
                'posSize' => [
                    'name' => 'positions.size',
                    'type' => 'hidden',
                    'source' => [
                        'type' => 'options',
                        'query' => 'size'
                    ]
                ]
            ]
        ];
        $filterTypes=[
            'client_index' => ['status', 'express', 'created'],
            'def' => ['status', 'express', 'created', 'approved'],
            'index' => ['client', 'status', 'express', 'created', 'approved'],
            'package' => ['ways', 'approved', 'status', 'express' ],
            'production' => ['generated'],
            'productions_form' => ['client', 'approved', 'express'],
            'productions_table' => ['hidden-toProduction'],
            'service' => ['status', 'express', 'created'],
            'table_client' => ['hidden-client'],
        ];
        $idx=array_key_exists($type, $filterTypes) ? $type : 'def';
        $filters=$this->choiceFilters($filterTypes[$idx], $defined); 
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

    public function getSettingsFilters(string $type='index'):array
    {
        $defined= [
            'client' => [
                'name' => 'client',
                'source' => [
                    'type' => 'entity',
                    'query' => 'Clients',
                ],
                'add' => [
                    'start' => [
                        ['v' => 'null', 'n' => 'globalnie']
                    ]
                ],
                'attr' => [
                    'multiple' => 'multiple'
                ],
                'd' => [
                    'widget' => 'multiselect',
                    'def-value' => ['null']               
                ]
            ] 
        ];
        $filterTypes=[
            'index' => ['client'],
            'table_client' => ['hidden-client']
        ];
        $idx=array_key_exists($type, $filterTypes) ? $type : 'index';
        return $this->choiceFilters($filterTypes[$idx], $defined); 
    }

    public function getProductionsFilters(string $type='index'):array
    {
        $defined= [
            'generated' => [
                'name' => 'generated',
                'type' => 'input',
                'setValue' => [
                    'type' => 'settings',
                    'query' => 'productions-filters-generated-value'
                ],
                'source' => [
                    'type' => 'settings',
                    'query' => 'productions-filters-dateRanges'
                ],
                'd' => [
                    'filter-options' => '{"type" : "date_period"}',
                    'widget' => 'daterange'
                ]
            ],
            'progress' => [
                'name' => 'progress',
                'label' => 'productions.label.progress',
                'source' => [
                    'type' => 'settings',
                    'query' => 'productions-filters-progress-dic'
                ],
                'setValue' => [
                    'type' => 'settings',
                    'query' => 'productions-filters-progress-value'
                ],
                'attr' => [
                ],
                'd' => [
                    'filter-options' => '{"type" : "between"}',
                    'widget' => 'combobox'
                ]
            ]   
        ];
        $filterTypes=[
            'index' => ['generated', 'progress']
        ];
        $idx=array_key_exists($type, $filterTypes) ? $type : 'index';
        return $this->choiceFilters($filterTypes[$idx], $defined);  
    }

    public function getPriceListsFilters(string $type='index'):array
    {
        $defined= [
            'start' => [
                'name' => 'start',
                'type' => 'input',
                'setValue' => [
                    'type' => 'settings',
                    'query' => 'pricelists-filters-start-value'
                ],
                'source' => [
                    'type' => 'settings',
                    'query' => 'pricelists-filters-dateRanges'
                ],
                'd' => [
                    'filter-options' => '{"type" : "date_period"}',
                    'widget' => 'daterange'
                ]
            ],
            'end' => [
                'name' => 'end',
                'type' => 'input',
                'setValue' => [
                    'type' => 'settings',
                    'query' => 'pricelists-filters-end-value'
                ],
                'source' => [
                    'type' => 'settings',
                    'query' => 'pricelists-filters-dateRanges'
                ],
                'd' => [
                    'filter-options' => '{"type" : "date_period"}',
                    'widget' => 'daterange'
                ]
            ],
            'clients' => [
                'name' => 'clients.id',
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
            ],
            'clientsGroups' => [
                'name' => 'clientsGroups.id',
                'source' => [
                    'type' => 'entity',
                    'query' => 'ClientsGroups',
                ],
                'attr' => [
                    'multiple' => 'multiple'
                ],
                'd' => [
                    'widget' => 'multiselect'                
                ]
            ],
            'hidden' => [
                'client' => [
                    'name' => 'clients.id',
                    'source' => [
                        'type' => 'options',
                        'query' => 'client'
                    ]
                ]   
            ]
                
        ];
        $filterTypes=[
            'index' => ['start', 'end', 'clientsGroups','clients'],
            'service' => ['start', 'end'],
            'table_client' => ['hidden-client']
        ];
        $idx=array_key_exists($type, $filterTypes) ? $type : 'service';
        return $this->choiceFilters($filterTypes[$idx], $defined);  
    }

    public function getDeliveriesFilters(string $type='index'):array
    {
        $defined= [
            'client' => 'client',
            'generated' => [
                'name' => 'generated',
                'type' => 'input',
                'setValue' => [
                    'type' => 'settings',
                    'query' => 'deliveries-filters-generated-value'
                ],
                'source' => [
                    'type' => 'settings',
                    'query' => 'deliveries-filters-dateRanges'
                ],
                'd' => [
                    'filter-options' => '{"type" : "date_period"}',
                    'widget' => 'daterange'
                ]
            ],
            'progress' => [
                'name' => 'progress',
                'label' => 'deliveries.label.progress',
                'source' => [
                    'type' => 'settings',
                    'query' => 'deliveries-filters-progress-dic'
                ],
                'setValue' => [
                    'type' => 'settings',
                    'query' => 'deliveries-filters-progress-value'
                ],
                'attr' => [
                ],
                'd' => [
                    'filter-options' => '{"type" : "between"}',
                    'widget' => 'combobox'
                ]
            ]
        ];
        $filterTypes=[
            'index' => ['client', 'generated', 'progress'] ,
            'service' => ['generated', 'progress'] ,
            'table_client' => ['hidden-client']
        ];
        $idx=array_key_exists($type, $filterTypes) ? $type : 'service';
        return $this->choiceFilters($filterTypes[$idx], $defined);  
    }

    public function getProductsFilters(string $type='index'):array
    {
        $defined= [
            "cutTime" => [
                'name' => 'cutTime',
                'data' => [
                    ['v' => 0, 'n' => 'przed'],
                    ['v' => 1, 'n' => 'po'],
                ],
                'd' => [
                    'widget' => 'multiselect',
                    'filter-options' => '{ "type" : "set"}',
                ],
            ],
            "packingTime" => [
                'name' => 'packingTime',
                'data' => [
                    ['v' => 0, 'n' => 'przed'],
                    ['v' => 1, 'n' => 'po'],
                ],
                'd' => [
                    'widget' => 'multiselect',
                    'filter-options' => '{ "type" : "set"}',
                ],
            ],
            'hidden' => [
                "production" => [
                    "name" => 'production',
                    'source' => [
                        'type' => 'options',
                        'query' => 'production'
                    ]
                ],
                "delivery" => [
                    "name" => 'delivery',
                    'source' => [
                        'type' => 'options',
                        'query' => 'delivery'
                    ]
                ]
            ]
        ];
        $filterTypes=[
            'index' => ['active', 'regular'],
            'productions_table' => ['hidden-production'],
            'deliveries_table' => ['hidden-delivery']
        ];
        $idx=array_key_exists($type, $filterTypes) ? $type : 'index';
        return $this->choiceFilters($filterTypes[$idx], $defined);  
    }

    public function getInvoicesFilters(string $type='index'):array
    {
        $defined= [
            'client' => 'client',
            'created' => [
                'name' => 'created',
                'type' => 'input',
                'setValue' => [
                    'type' => 'settings',
                    'query' => 'invoices-filters-created-value'
                ],
                'source' => [
                    'type' => 'settings',
                    'query' => 'invoices-filters-dateRanges'
                ],
                'd' => [
                    'filter-options' => '{"type" : "date_period"}',
                    'widget' => 'daterange'
                ]
                
            ],
            'term' => [
                'name' => 'term',
                'type' => 'input',
                'setValue' => [
                    'type' => 'settings',
                    'query' => 'invoices-filters-term-value'
                ],
                'source' => [
                    'type' => 'settings',
                    'query' => 'invoices-filters-dateRanges'
                ],
                'd' => [
                    'filter-options' => '{"type" : "date_period"}',
                    'widget' => 'daterange'
                ]
                
            ]
        ];
        $filterTypes=[
            'index' => ['client', 'created', 'term'],
            'service' => ['created', 'term'],
            'table-client' => ['hidden-client']
        ];
        $idx=array_key_exists($type, $filterTypes) ? $type : 'index';
        return $this->choiceFilters($filterTypes[$idx], $defined);  
    }

    public function getPriceListItemsFilters(string $type='index'):array
    {
        $defined= [
            'active' => 'active',
            'size' => [
                'name' => 'size.id',
                'label' => 'pricelistitems.label.filter.size',
                'source' => [
                    'type' => 'entity',
                    'query' => 'Sizes'
                ],
                'attr' => [
                    'multiple' => 'multiple'
                ],
                'd' => [
                    'widget' => 'multiselect'
                ]
            ],
            'color' => [
                'name' => 'color.id',
                'label' => 'pricelistitems.label.filter.color',
                'source' => [
                    'type' => 'entity',
                    'query' => 'Colors'
                ],
                'attr' => [
                    'multiple' => 'multiple'
                ],
                'd' => [
                    'widget' => 'multiselect'
                ]
            ]
        ];
        $filterTypes=[
            'index' => ['active', 'size', 'color'],
            'pricelists_form' => ['size', 'color'],
            'def' => ['size', 'color']
        ];
        $idx=array_key_exists($type, $filterTypes) ? $type : '';
        return $this->choiceFilters($filterTypes[$idx], $defined);  
    }


}