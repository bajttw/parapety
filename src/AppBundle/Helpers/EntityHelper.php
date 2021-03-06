<?php

namespace AppBundle\Helpers;

// use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Component\DependencyInjection\ContainerInterface ;
use AppBundle\Utils\Utils;

class EntityHelper{

    private $entitiesSettings=[];

    private $bundleName='AppBundle';
    private $entityClassName='';
    public $entitiesClasses = ['Clients', 'Settings', 'Uploads', 'Users', 'Notes', 'Colors', 'Models', 'Sizes', 'Trims', 'Orders', 'Positions', 'Products', 'Productions', 'Deliveries', 'Invoices', 'PriceLists', 'PriceListItems', 'Prices'];
   
    private $em;
    private $sh;
    private $idPrototype='__id__';

    public function __construct(EntityManagerInterface $entityManager, SettingsHelper $settingsHelper){
        $this->sh=$settingsHelper;
        $this->em=$entityManager;
    }
    // public function __construct(EntityManager $entityManager, SettingsHelper $settingsHelper){
    //     $this->sh=$settingsHelper;
    //     $this->em = $entityManager;
    // }

    public function setEntityClassName(string $entityClassName){
        $this->entityClassName= $entityClassName;
    }

    public function getBundleName():string
    {
        return $this->bundleName;
    }

    public function getEntityClassName(?string $entityClassName=null){
        if($entityClassName === ''){
            return '';
        }
        return ($entityClassName) ?: $this->entityClassName;
    }

    public function getNamespace(string $name, ?string $entityClassName=null, $suffix = '', ?string $bundleName=null): string
    {
        $entityClassName= ($entityClassName) ?: $this->entityClassName;
        $str = explode(':', $entityClassName);
        if (count($str) > 1) {
            $bundleName = $str[0];
            $entityClassName = $str[1];
        }
        return (is_null($bundleName) ? $this->bundleName : $bundleName) . '\\' . ucfirst($name) . '\\' . $entityClassName . $suffix;
    }

    public function getRepositoryNamespace(?string $entityClassName=null, string $bundleName=null ):string
    {
        return $this->getNamespace('Repository', $entityClassName, 'Repository');
    }

    public function getControllerNamespace(?string $entityClassName=null, string $bundleName=null ):string
    {
        return $this->getNamespace('Controller', $entityClassName, 'Controller');
    }

    public function getEntityNamespace(?string $entityClassName=null, string $bundleName=null):string
    {
        return $this->getNamespace('Entity', $entityClassName);
    }

    public function getEntityName(?string $entityClassName=null, string $bundleName=null):string
    {
        if($entityClassName === ''){
            return '';
        }
        return ($this->getEntityNamespace($entityClassName))::en;
    }

    public function getIdPrototype(?string $entityClassName=null, string $bundleName=null):string
    {
        return is_null($entityClassName) ? $this->idPrototype : $this->getEntityNamespace($entityClassName)::idPrototype;
    }

    // public function getEmptyId(?string $entityClassName=null, string $bundleName=null):string//deprected
    // {
    //     return ($this->getEntityNamespace($entityClassName))::emptyId;
    // }

    public function getEntityShortNames(?string $entityClassName=null, string $bundleName=null):array
    {
        return ($this->getEntityNamespace($entityClassName))::$shortNames;
    }

    public function getFormNamespace(?string $entityClassName=null, string $bundleName=null):string
    {
        return $this->getNamespace('Form', $entityClassName, 'Type');
    }

    public function getDic(?string $entityClassName=null){
        $entityClassName= ($entityClassName) ?: $this->entityClassName;
        if(!Utils::deep_array_key_exists(['dictionaries', $entityClassName], $this->entitiesSettings)){
            $this->entitiesSettings['dictionaries'][$entityClassName]=$this->getRepository($entityClassName)->getDic();
        }
        return $this->entitiesSettings['dictionaries'][$entityClassName];
    }

    // public function getEntityClass(?string $entityClassName=null):string
    // {
    //     return ($this->getEntityNamespace($entityClassName))::class;
    // }

    public function getRepository(?string $entityClassName=null)
    {
        return $this->em->getRepository($this->getEntityNamespace($entityClassName));
    }

    public function getCount(?string $entityClassName=null):int
    {
        return $this->getRepository($entityClassName)->getCount();
    }
// </editor-fold>  

//<editor-fold defaultstate="collapsed" desc="EntitySettings">
    private function genSettings(string $entityClassName):void
    {
        $en=$this->getEntityName($entityClassName);
        $this->entitiesSettings[$entityClassName]=array_merge($this->sh->getSettingsValues($en), [
            'en' => $en,
            'ecn' => $entityClassName,
            'eid' => $this->getIdPrototype($entityClassName),
            'fields' => $this->getEntityShortNames($entityClassName)
        ]);
        $genCustom='custom'.$entityClassName.'Settings';
        if(\method_exists($this, $genCustom)){
            $this->$genCustom();
        }            
    }
 
    public function getSettingsValue(string $name, ?string $entityClassName=null)
    {
        return Utils::deep_array_value($name, $this->getSettings($entityClassName));
    }
 
    public function getSettings(?string $entityClassName=null): array
    {
        $entityClassName= ($entityClassName) ?: $this->entityClassName;
        if(!array_key_exists($entityClassName, $this->entitiesSettings)){
            $this->genSettings($entityClassName);
        }      
        return $this->entitiesSettings[$entityClassName];
    }

    private function genEntitiesSettings(array $entitiesClasses = null):void
    {
        $entitiesClasses= ($entitiesClasses) ?: $this->entitiesClasses;
        $this->entitiesSettings = [];
        foreach ($entitiesClasses as $ec) {
            $this->genEntitySettings($ec);
        }
    }

    public function getEntitiesSettings(array $entitiesClasses=null):array
    {
        if(is_null($entitiesClasses)){
            foreach ($this->entitiesClasses as $ec) {
                if(!array_key_exists($ec, $this->entitiesSettings)){
                    $this->genSettings($ec);
                }      
            }                
            return $this->entitiesSettings;
        }
        $entitiesSettings=[];
        foreach ($entitiesClasses as $ec) {
            $entitiesSettings[$ec]=$this->getEntitySettings($ec);
        }
        return $entitiesSettings;
    }

    public function getNumerate(?string $entityClassName=null, ?int $clientId=null):array
    {
        $numerate=[
            'nr' => null,
            'numberGenerator' => null
        ];
        $en=$this->getEntityName($entityClassName);
        $nrName=$en.'-number';
        $nrGeneratorName=$en.'-numberGenerator';
        $es=$this->getSettings($entityClassName);
        if(!is_null($clientId)){
            $numerate['nr']=$this->sh->getClientSettingValue($clientId, $nrName);   
            $numerate['numberGenerator']=$this->sh->getClientSettingValue($clientId, $nrGeneratorName);
        }
        if(is_null($numerate['nr'])){
            $nr=$this->sh->getSettingValue($nrName);
        }      
        if(is_null($numerate['numberGenerator'])){
            $numerate['numberGenerator']=Utils::deep_array_value('numberGenerator', $es, []);
        }      
        return $numerate;

    }

    public function saveEntityNumber(string $entityClassName, int $nr, ?int $clientId=null){
        $nrName=$this->getEntityName($entityClassName).'-number';
        if (is_null($clientId) || is_null($this->sh->getClientSettingValue($clientId, $nrName))) {
            $this->sh->saveSetting( $nrName, $nr);
        }else{
            $this->sh->saveSettingForClient($clientId, $nrName, $nr);
        }
    }
// </editor-fold>  

//<editor-fold defaultstate="collapsed" desc="Custom functions">
    private function customPriceListItemsSettings():void
    {
        $this->addDics($this->entitiesSettings['PriceListItems'], [ 'Colors', 'Sizes']);
    }

    private function customOrdersSettings():void
    {
        $this->addDics($this->entitiesSettings['Orders'], ['Models' , 'Colors', 'Trims', 'Sizes']);
    }

    private function customPriceListsSettings():void
    {
        $this->addDics($this->entitiesSettings['PriceLists'], ['PriceListItems' ]);
    }

    private function customPricesSettings():void
    {
        $this->addDics($this->entitiesSettings['Prices'], ['PriceListItems' ]);
    }

    private function addDics(array &$settings, array $entitiesClasses):void
    {
        foreach ($entitiesClasses as $ecn){
            $settings['dictionaries'][$ecn]=$this->getDic($ecn);            
        }          
    }

// </editor-fold>  

//<editor-fold defaultstate="collapsed" desc="Predefined elements">

    public function getElements(string $elementsType, ?string $entityClassName= null):?array
    {
        return Utils::deep_array_value([$this->getEntityClassName($entityClassName), $elementsType], $this->elements);
    }

    private $elements = [
        'Clients' =>[
            'filters' => [
                'predefined' => [
                    'active' => 'active',
                    'users' =>[
                        'name' => 'users.id',
                        'dataSource' => [
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
        
                ],
                'types' => [
                    'index' => ['active', 'regular'],
                    'def' => ['active', 'regular'],
                    'table_service' => ['hidden-active']
                ]
            // ],
            // 'actions' => [
            //     'predefined' => [
            //         'edit' => [ 
            //             'action' => 'edit', 
            //             'type' => 'm'
            //         ],
            //         'delete' => [ 
            //             'action' => 'delete',
            //             'type' => 'm'
            //         ]
            //     ],
            //     'types'=>[
            //         'index' => ['edit', 'delete']
            //     ]
            ]
        ],
        'Colors' =>[
            'filters' => [
                'predefined' => [
                ],
                'types' => [
                    'index' => ['active'],
                    'def' => ['active']
                ]
            ],
            // 'actions' => [
            //     'predefined' => [               
            //         'edit' => [ 
            //             'action' => 'edit', 
            //             'type' => 'm', 
            //             'target' => 'colors'
            //         ],
            //         'delete' => [ 
            //             'action' => 'delete',
            //             'type' => 'm',
            //             'target' => 'colors'
            //         ]
            //     ],
            //     'types'=>[
            //         'index' => ['edit', 'delete'],
            //         'def' => ['edit', 'delete']
            //     ]
            // ],
            // 'tools' => [
            //     'predefined' => [               
            //         'new' => [ 
            //             'action' => 'new', 
            //             'type' => 'm', 
            //             'target' => 'colors'
            //         ]
            //     ],
            //     'types'=>[
            //         'index' => ['new'],
            //         'def' => ['new']
            //     ]
            // ]
        ],
        'Deliveries' => [
            'filters' => [
                'predefined' => [
                    'client' => 'client',
                    'generated' => [
                        'name' => 'generated',
                        'type' => 'input',
                        'setValue' => [
                            'type' => 'settings',
                            'query' => 'deliveries-filters-generated-value'
                        ],
                        'dataSource' => [
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
                        'dataSource' => [
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
                    ],
                    'hidden' => [
                        'client' => 'hidden-client'
                    ]
                ],
                'types' => [
                    'index' => ['client', 'generated', 'progress'] ,
                    'service' => ['generated', 'progress'] ,
                    'def' => ['generated', 'progress'] ,
                    'table_client' => ['hidden-client'],
                    'table_service' => ['hidden-client']
                ]
            ]
        ],
        'Invoices' => [
            'filters' => [
                'predefined' => [
                    'client' => 'client',
                    'created' => [
                        'name' => 'created',
                        'type' => 'input',
                        'setValue' => [
                            'type' => 'settings',
                            'query' => 'invoices-filters-created-value'
                        ],
                        'dataSource' => [
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
                        'dataSource' => [
                            'type' => 'settings',
                            'query' => 'invoices-filters-dateRanges'
                        ],
                        'd' => [
                            'filter-options' => '{"type" : "date_period"}',
                            'widget' => 'daterange'
                        ]
                    ],
                    'hidden' => [
                        'client' => 'hidden-client'
                    ]
                ],
                'types' => [
                    'index' => ['client', 'created', 'term'],
                    'def' => ['created', 'term'],
                    'service' => ['created', 'term'],
                    'table_client' => ['hidden-client'],
                    'table_service' => ['hidden-client']
                ]
            ]   
        ],
        'Models' =>[
            'filters' => [
                'predefined' => [
                ],
                'types' => [
                    'index' => ['active'],
                    'def' => ['active']
                ]
            // ],
            // 'actions' => [
            //     'predefined' => [
            //         'edit' => [ 
            //             'action' => 'edit', 
            //             'type' => 'm', 
            //             'target' => 'models'
            //         ],
            //         'delete' => [ 
            //             'action' => 'delete',
            //             'type' => 'm',
            //             'target' => 'models'
            //         ]
            //     ],
            //     'types'=>[
            //         'index' => ['edit', 'delete'],
            //         'def' => ['edit', 'delete']
            //     ]
            ]
        ],
        'Orders' => [
            'filters' => [
                'predefined' => [
                    'client' => 'client',
                    'status' => [
                        'name' => 'status',
                        'dataSource' => [
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
                        'dataSource' => [
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
                        'dataSource' => [
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
                        'dataSource' => [
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
                        'dataSource' => [
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
                        'client' => 'hidden-client',
                        'toProduction' => [
                            'name' => 'production',
                            'valueSource' => [
                                'type' => 'options',
                                'query' => 'production',
                                'valueType' => 'set_or_null'
                            ]
                        ],
                        'production' => [
                            'name' => 'production',
                            'value' => null,
                            'options' => ['not' => true]
                        ],
                        'notState' => [
                            'name' => 'status',
                            'valueSource' => [
                                'type' => 'options',
                                'query' => 'bannedStates'
                            ],
                            'options' => ['not' => true]
                        ],
                        'posSize' => [
                            'name' => 'positions.size',
                            'valueSource' => [
                                'type' => 'options',
                                'query' => 'size'
                            ]
                        ]
                    ]
                ],
                'types' => [
                    'client_index' => ['status', 'express', 'created'],
                    'def' => ['status', 'express', 'created', 'approved'],
                    'index' => ['client', 'status', 'express', 'created', 'approved'],
                    'form_deliveries' => ['client', 'approved', 'express'],
                    'form_productions' => ['client', 'approved', 'express'],
                    'form_deliveries_table' => ['hidden-toProduction'],
                    'form_productions_table' => ['hidden-toProduction'],
                    'service' => ['status', 'express', 'created'],
                    'table_client' => ['hidden-client'],
                    'table_service' => ['hidden-client']
                ]                          
            ],
            'actions' => [
                'predefined' => [
                    'edit' => [
                        'action' => 'edit', 
                        'renderType' => 'w'
                    ],
                    'copy' => [
                        'action' => 'copy', 
                        'browserAction' => true,
                        'd' => [
                            'exp' => ''
                        ]
                    ],
                    'show' => [
                        'action' => 'show', 
                        'browserAction' => true
                    ],
                    'pdf' => [
                        'action' => 'pdf', 
                        'browserAction' => true,
                        'd' => [
                            'exp' => 'order'
                        ]
                    ],
                    'xls' => [
                        'action' => 'xls', 
                        'browserAction' => true,
                        'd' => [
                            'exp' => ''
                        ]
                    ]       
                ],
                'types' => [
                    'index' => ['show', 'pdf', 'edit', 'delete'],
                    'def' => ['show']
                ]

            ],
            'tools' => [
                'predefined' => [
                    'new' => [   
                        'action' => 'new',
                        'attr' => [
                            'class' => 'btn-primary'
                        ],
                        'renderType' => 'w'
                    ]
                ],
                'types' => [
                    'index' => ['new'],
                    'def' => ['new']
                ]
         
            ],
            'templates' => [    
                'generic' => ['edit', 'Window/edit', 'show', 'Modal/show', 'import', 'Window/import']
            ]

        ],
        'PriceListItems' => [
            'filters' => [
                'predefined' => [
                    'active' => 'active',
                    'size' => [
                        'name' => 'size.id',
                        'label' => 'pricelistitems.label.filter.size',
                        'dataSource' => [
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
                        'dataSource' => [
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
                ],
                'types' => [
                    'index' => ['active', 'size', 'color'],
                    'form_pricelists' => ['size', 'color'],
                    'def' => ['size', 'color']
                ]
            ]    
        ],
        'PriceLists' => [
            'filters' => [
                'predefined' => [
                    'start' => [
                        'name' => 'start',
                        'type' => 'input',
                        'setValue' => [
                            'type' => 'settings',
                            'query' => 'pricelists-filters-start-value'
                        ],
                        'dataSource' => [
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
                        'dataSource' => [
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
                        'dataSource' => [
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
                        'dataSource' => [
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
                            'valueSource' => [
                                'type' => 'options',
                                'query' => 'client'
                            ]
                        ]   
                    ]
                        
                ],
                'types' => [
                    'index' => ['start', 'end', 'clientsGroups','clients'],
                    'service' => ['start', 'end'],
                    'def' => ['start', 'end'],
                    'table_client' => ['hidden-client'],
                    'table_service' => ['hidden-client']
                ]
            ]
        ],
        'Productions' => [
            'filters' => [
                'predefined' => [
                    'generated' => [
                        'name' => 'generated',
                        'type' => 'input',
                        'setValue' => [
                            'type' => 'settings',
                            'query' => 'productions-filters-generated-value'
                        ],
                        'dataSource' => [
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
                        'dataSource' => [
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
                ],
                'types' => [
                    'index' => ['generated', 'progress']
                ]
            ]    
        ],
        'Products' => [
            'filters' => [
                'predefined' => [
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
                            'valueSource' => [
                                'type' => 'options',
                                'query' => 'production'
                            ]
                        ],
                        "delivery" => [
                            "name" => 'delivery',
                            'valueSource' => [
                                'type' => 'options',
                                'query' => 'delivery'
                            ]
                        ]
                    ]
                ],
                'types' => [
                    'index' => ['active', 'regular'],
                    'form_productions_table' => ['hidden-production'],
                    'form_deliveries_table' => ['hidden-delivery']
                ]
            ],
            'actions' => [
                'predefined' => [               
                ],
                'types'=>[
                    'index' => ['show'],
                    'def' => ['show']
                ]
            ]
        ],
        'Settings' => [
            'filters' => [
                'predefined' => [
                    'client' => [
                        'name' => 'client',
                        'dataSource' => [
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
                    ],
                    'hidden' => [
                        'client' => 'hidden-client'
                    ]
                ],
                'types' => [
                    'index' => ['client'],
                    'service' => [],
                    'table_client' => ['hidden-client'],
                    'table_service' => ['hidden-client']
                ]            
            ]
        ],
        'Sizes' =>[
            'filters' => [
                'predefined' => [
                ],
                'types' => [
                    'index' => ['active'],
                    'def' => ['active']
                ]
            // ],
            // 'actions' => [
            //     'predefined' => [
            //         'edit' => [ 
            //             'action' => 'edit', 
            //             'type' => 'm', 
            //             'target' => 'sizes'
            //         ],
            //         'delete' => [ 
            //             'action' => 'delete',
            //             'type' => 'm',
            //             'target' => 'sizes'
            //         ]
            //     ],
            //     'types'=>[
            //         'index' => ['edit', 'delete'],
            //         'def' => ['edit', 'delete']
                // ]
            ]
        ],
        'Trims' =>[
            'filters' => [
                'predefined' => [
                ],
                'types' => [
                    'index' => ['active'],
                    'def' => ['active']
                ]
            // ],
            // 'actions' => [
            //     'predefined' => [
            //         'edit' => [ 
            //             'action' => 'edit', 
            //             'type' => 'm', 
            //             'target' => 'trims'
            //         ],
            //         'delete' => [ 
            //             'action' => 'delete',
            //             'type' => 'm',
            //             'target' => 'trims'
            //         ]
            //     ],
            //     'types'=>[
            //         'index' => ['edit', 'delete'],
            //         'def' => ['edit', 'delete']
            //     ]
            ]
        ]
    ];




 // </editor-fold>  
}