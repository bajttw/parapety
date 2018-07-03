<?php
namespace AppBundle\Controller;

use AppBundle\Utils\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Orders;
use AppBundle\Entity\Uploads;
use AppBundle\Entity\Positions;

class OrdersController extends AppController
{

    const en = 'orders';
    const ec = 'Orders';
    

    public $ownTemplate = ['edit', 'Window/edit', 'show', 'Modal/show', 'import', 'Window/import'];
    public $formExport = true;
    public static $importFields=['id', 'number', 'created'];
    
    // public static function getFilters($type = 'index', $options = [])
    // {
    //     $id = Utils::deep_array_value('id', $options);
    //     $cid = Utils::deep_array_value('cid', $options);
    //     $isClient=$cid != null;
    //     $filters = [];
    //     $fs = [
    //         'client' => [
    //             'name' => 'client',
    //             'source' => [
    //                 'type' => 'entity',
    //                 'query' => 'Clients',
    //             ],
    //             'attr' => [
    //                 'multiple' => 'multiple'
    //             ],
    //             'd' => [
    //                 'widget' => 'multiselect'                
    //             ]
    //         ],
    //         'status' => [
    //             'name' => 'status',
    //             'source' => [
    //                 'type' => 'settings',
    //                 'query' => 'orders-dictionaries-status'
    //             ],
    //             'attr' => [
    //                 'multiple' => 'multiple'
    //             ],
    //             'd' => [
    //                 'widget' => 'multiselect'                
    //             ]
    //         ],
    //         'express' => [
    //             'name' => 'express',
    //             'source' => [
    //                 'type' => 'settings',
    //                 'query' => 'orders-dictionaries-express'
    //             ],
    //             'attr' => [
    //                 'multiple' => 'multiple'
    //             ],
    //             'd' => [
    //                 'widget' => 'multiselect'
    //             ]
    //         ],
    //         'created' => [
    //             'name' => 'created',
    //             'type' => 'input',
    //             'setValue' => [
    //                 'type' => 'settings',
    //                 'query' => 'orders-filters-created-value'
    //             ],
    //             'source' => [
    //                 'type' => 'settings',
    //                 'query' => 'orders-filters-dateRanges'
    //             ],
    //             // 'attr' => [
    //             //     'data-daterange' => json_encode([]),
    //             //     'data-filter-options' => json_encode(['type' => 'date_period']),
    //             // ],
    //             'd' => [
    //                 'filter-options' => json_encode(['type' => 'date_period']),
    //                 'widget' => 'daterange'
    //             ]
                
    //         ],
    //         'approved' => [
    //             'name' => 'approved',
    //             'type' => 'input',
    //             'setValue' => [
    //                 'type' => 'settings',
    //                 'query' => 'orders-filters-approved-value'
    //             ],
    //             'source' => [
    //                 'type' => 'settings',
    //                 'query' => 'orders-filters-dateRanges'
    //             ],
    //             'd' => [
    //                 'filter-options' => json_encode(['type' => 'date_period']),
    //                 'widget' => 'daterange'
    //             ]
                
    //         ],
    //         'ways' => [
    //             'name' => 'client.ways.id',
    //             'label' => 'orders.label.filter.ways',
    //             'source' => [
    //                 'type' => 'entity',
    //                 'query' => 'Ways',
    //             ],
    //             'attr' => [
    //                 'multiple' => 'multiple'
    //             ],
    //             'd' => [
    //                 'widget' => 'multiselect'
    //             ]
    //         ],
    //         'not_package' => [
    //             'name' => 'positions.package',
    //             'type' => 'hidden',
    //             'value' => $id == null ? null : [null, $id],
    //         ],
    //         'package' => [
    //             'name' => 'positions.package',
    //             'type' => 'hidden',
    //             'value' => null,
    //             'options' => ['not' => true]
    //         ],
    //         'not_production' => [
    //             'name' => 'production',
    //             'type' => 'hidden',
    //             'value' => $id == null ? null : [null, $id],
    //         ],
    //         'production' => [
    //             'name' => 'production',
    //             'type' => 'hidden',
    //             'value' => null,
    //             'options' => ['not' => true]
    //         ],
    //         'not_shipment' => [
    //                 'name' => 'shipment',
    //                 'type' => 'hidden',
    //             'value' => $id == null ? null : [null, $id],
    //         ],
    //         'shipment' => [
    //             'name' => 'shipment',
    //             'type' => 'hidden',
    //             'value' => null,
    //             'options' => ['not' => true]
    //         ],
    //         'notState' => [
    //             'name' => 'status',
    //             'type' => 'hidden',
    //             'value' =>  Utils::deep_array_value('bannedStates', $options),
    //             'options' => ['not' => true]
    //         ],
    //         'posSize' => [
    //             'name' => 'positions.size',
    //             'type' => 'hidden',
    //             'value' => Utils::deep_array_value('size', $options)
    //         ],
    //         'posPackage' => [
    //             'operator' => 'or',
    //             'name' => "positions.package",
    //             'type' => 'hidden',
    //             'value' => Utils::deep_array_value('package', $options)
    //         ],
    //         'posNoPackage' => [
    //             'name' => "positions.package",
    //             'type' => 'hidden',
    //             'value' => null
    //         ]
    //     ];
        
    //     switch ($type) {
    //         case 'index' :
    //             foreach (['client', 'status', 'express', 'created', 'approved'] as $f) {
    //                 self::addFilter($filters, $fs[$f], $f);
    //             }
    //         break;
    //         case 'productions_form' :
    //             foreach (['client', 'approved', 'express'] as $f) {
    //                 self::addFilter($filters, $fs[$f], $f);
    //             }
    //         break;
    //         case 'client_index':
    //         foreach (['status', 'express', 'created'] as $f) {
    //             self::addFilter($filters, $fs[$f], $f);
    //         }
    //         break;
    //         case 'table_client':
    //             self::addFilter($filters, self::genFilter('client_hidden', $options), 'client');
    //         break;
        
    //         case 'service' :
    //             foreach (['status', 'express', 'created'] as $f) {
    //                 self::addFilter($filters, $fs[$f], $f);
    //             }
    //         break;
    //         case 'package' :
    //             $fs['status']['banned']='orders-filters-status-banned-package';
    //             foreach (['ways', 'approved', 'status', 'express' ] as $f) {
    //                 self::addFilter($filters, $fs[$f], $f);
    //             }
    //         break;
    //         case 'packageHiddens' :
    //             self::addFilter($filters, $fs['posNoPackage'], 'posNoPackage');
    //             if ($fs['notState']['value']) {
    //                 self::addFilter($filters, $fs['notState'], 'notState');
    //             }
    //             if ($fs['posSize']['value']) {
    //                 self::addFilter($filters, $fs['posSize'], 'posSize');
    //             }
    //             if ($fs['posPackage']['value']) {
    //                 self::addFilter($filters, $fs['posPackage'], 'posPackage');
    //             }
    //         break;
    //         case 'production' :
    //             foreach (['generated'] as $f) {
    //                 self::addFilter($filters, $fs[$f], $f);
    //             }
    //         break;
    //         case 'shipment' :
    //             foreach (['generated', 'production', 'not_shipment'] as $f) {
    //                 self::addFilter($filters, $fs[$f], $f);
    //             }
    //         break;
    //         default :
    //             foreach (['client', 'status', 'created'] as $f) {
    //                 self::addFilter($filters, $fs[$f], $f);
    //             }
    //     }
    //     return $filters;
    // }

    // public static function getActions($type = 'view', $options=[])
    // {
    //     $actions = [];
    //     $all = [
    //         'edit' => ['action' => 'edit', 'type' => 'w'],
    //         // 'show' =>  [ 'action' => 'show', 'type' => 'm', 'target' => 'orders'],
    //         'copy' => [
    //             'action' => 'copy', 
    //             'browserAction' => true,
    //             'd' => [
    //                 'exp' => ''
    //             ]
    //         ],
    //         'show' => ['action' => 'show', 'browserAction' => true],
    //         'pdf' => [
    //             'action' => 'pdf', 
    //             'browserAction' => true,
    //             'd' => [
    //                 'exp' => 'order'
    //             ]
    //         ],
    //         // 'xls' => [
    //         //     'action' => 'xls', 
    //         //     'browserAction' => true,
    //         //     'd' => [
    //         //         'exp' => ''
    //         //     ]
    //         // ],
    //         'delete' => ['action' => 'delete', 'type' => 'm', 'target' => 'orders']
    //     ];
    //     switch ($type) {
    //         case 'index' :
    //             $as = ['show', 'copy', 'pdf', 'edit', 'delete'];
    //         break;
    //         case 'view' :
    //         default :
    //             $as = ['show', 'copy', 'pdf'];
    //     }
    //     foreach ($as as $a) {
    //         $actions[$a] = $all[$a];
    //     }
    //     return $actions;
    // }

    public static function getToolbarBtn($type='index', $options=[] )
    {
        $cid = Utils::deep_array_value('cid', $options);
        $b= [
            'new' => [   
                'action' => 'new',
                'isClient' => $cid ? true : false,
                'attr' => [
                    'class' => 'btn-primary',
                    'target' => '_blank'
                ],
                'routeParam' => array_merge( [ 'type' => 'w' ],
                $cid ? [ 'cid' => $cid ] : [] )
            ],
            'import' =>[   
                'action' => 'import',
                'attr' => [
                    'class' => 'btn-primary',
                    'target' => '_blank'
                ]
            ],
            'production' =>[   
                'action' => 'production',
                'attr' => [
                    'class' => 'btn-primary',
                    'href' => '#'
                ],
                'd' => [
                    'modal-widget' => 'bajtModalTableExport',
                    'target' => Utils::deep_array_value( 'productions-modalTableExport', $options, '#'.(static::en).'_table_export_modal')
                ]
            ]
        ];
        $btns=[];
        switch($type){
            case 'index':
                foreach(['new', 'import', 'production'] as $n){
                    $btns[]=$b[$n];
                }
            break;
            default:
                foreach(['new'] as $n){
                    $btns[]=$b[$n];
                }
        }
        return $btns;
        
    }
    
    public static function getModal()
    {
        return [
            'name' => self::en, 
            // 'attr' =>[
            //     'class' => 'ajax'
            // ],
            'dialog_attr' => [
                'class' => 'modal-xl'
            ]
        ];
    }

 //  <editor-fold defaultstate="collapsed" desc="Custom functions">
    
    protected function customMessages(&$messages, $type){

        switch ($type){
            case 'create':
            case 'update':
                $messages['message'].=" <i>".$this->entity->getNumber()."</i>";
            break;
                
        }
        return $messages;
    }

    protected function customCreateAction(&$dataReturn){
        $dataReturn['toEdit'] = true;
        return $dataReturn;
    }

    protected function customEditAction(Request $request, int $id, int $cid = 0):void
    {
        $this->renderOptions['entity_data'] = $this->entity->getShowData();
    }
    
    protected function newEntity(array $options = [] ):void
    {
        parent::newEntity($options);
        if ($this->isClient()) {
            $this->entity->setClient($this->client);        
        }
        // $this->entity->genTerm(Utils::deep_array_value(['dictionaries', 'terms'], $this->getEntitySettings()));
        $this->entity->addPosition(new Positions());
    }

    // protected function preUpdateAction(Request $request, $id, $cid = 0){
    //     $this->entity->saveOldPositions();
    // }

    // protected function customUpdateAction(&$dataReturn){
    //     $this->entity->checkPositions($this->getEntityManager());
    //     return $dataReturn;
    // }

    protected function setCustomFormOptions()
    {
        $this->formOptions['attr']['data-admin'] = $this->isAdmin();
        $this->formOptions['attr']['data-form'] = self::en;
        $this->formOptions['client_choice'] = ! ($this->isClient() || $this->entity->getClient());
        $this->renderOptions['upload_route'] = 'uploads_uploadfile';
        $this->setReturnUrl();
        $this->formOptions['attr']['data-copy-textarea'] = self::en;
        $this->formOptions['attr']['style'] = "min-width: 1200px;";
        $this->addModalsField([
            'comment' => [ 
                'field' => [
                    'type' => 'textarea'
                ]
            ],
            'model' => [ 
                'ecn' => 'Models', 
                'field' => [
                    'type' => 'radio'
                ],
                'dataSource' => [
                    'type' => 'entity'
                ]
            ],
            'color' => 
            [ 
                'ecn' => 'Colors', 
                'field' => [
                    'type' => 'radio'
                ],
                'dataSource' => [
                    'type' => 'entity'
                ]
            ],
            'size' => [ 
                'ecn' => 'Sizes', 
                'field' => [
                    'type' => 'radio'
                ],
                'dataSource' => [
                    'type' => 'entity'
                ]
            ],
            'trims' => [ 
                'ecn' => 'Trims',
                'd' => [
                    'modal-widget' => 'trims'
                ],
                'field' => [
                    'type' => 'checkbox',

                ],
                'dataSource' => [
                    'type' => 'entity'
                ],
                'attr' => [
                    'class' => 'modal-trims'
                ]
            ]
        ])
        ->addExpModal()
        // ->addModal(
        //     [
        //     'name' => 'import', 
        //     'content' => $this->tmplPath('importPositions', 'Orders'),
        //         'attr' => [
        //             'class' => 'modal-import',
        //         ],
        //         'dialog_attr' => [
        //          'class' => 'modal-xl',
        //         ]

        //     ]
        // );
        ;
        return $this;
    }
    

    // public static function genCustomSettings($controller, &$entitySettings=[]){
    //     foreach (['Models' , 'Colors', 'Trims', 'Sizes'] as $ecn){
    //         $entitySettings['dictionaries'][$ecn]=$controller->getDic($ecn);            
    //     }  
    //     return $entitySettings;
    // }
    
 // </editor-fold>   
    
 //  <editor-fold defaultstate="collapsed" desc="Actions">

    //    public function ajaxDataAction(Request $request, $id, $cid=0, $pid=0){
    //        $this->entityQuery="e.id, e.number as nr, e.clientNumber as cnr, DATE_FORMAT(e.generated, '%Y-%m-%d') as g, DATE_FORMAT(e.approved, '%Y-%m-%d') as ap, DATE_FORMAT(e.term, '%Y-%m-%d') as t, e.status as a_s, e.express as exp, e.quantity as q, e.area as a, e.progress as pr";
    //        return parent::ajaxDataAction($request, $id, $cid);
    //    }
    
    public function ajaxListToProductionAction(Request $request, $cid = 0){
        if (!$this->preAction($request, $cid, ['entitySettings' => false, 'checkPrivilages' => 1 ])) {
            return $this->responseAccessDenied(true);
        }
        // $this->setEntityQuery();
        return new JsonResponse($this->getEntiesFromBase($request, 'getListToProduction'));
    }

    public function ajaxListToDeliveryAction(Request $request, $cid = 0){
        if (!$this->preAction($request, $cid, ['entitySettings' => false, 'checkPrivilages' => 1 ])) {
            return $this->responseAccessDenied(true);
        }
        // $this->setEntityQuery();
        return new JsonResponse($this->getEntiesFromBase($request, 'getListToDelivery'));
    }

    public function indexAction(Request $request, $cid = 0)
    {
        if (!$this->preAction($request, $cid, [ 'checkPrivilages' => 0 ])) {
            return $this->responseAccessDenied();
        }
        $this->setTemplate('index');
        $this->setRenderOptions([
            'title' => $this->getTransHelper()->titleText('index'),
            'toolbars' => [
                $this->genToolbar(),
                $this->genFilterbar()
            ],
            'table' => $this->genDT('index', static::ec, [
                'actions' => 'index',
                'select' => [
                    'options' => [
                        'style' => 'multi'
                    ],
                    'column' => true
                ]
            ])
        ])
            ->addEntityModal()
            ->addTableExportModal( static::ec, [
                'title' => $this->getTransHelper()->modalTitle('generate', 'Productions'),
                'columns' => static::$importFields,
                'd' => [
                    'options' => [
                        'dataTableName' => static::en,
                        'importFields' => static::$importFields,
                        'ajax' => [
                            'type' => 'POST',
                            'url' => $this->getRouteHelper()->getEmployeeUrl('generate', 'Productions')
                        ]
                    ]
                ]
            ] );
        // ->generateEntityFilters();
        return $this->renderSystem();
    }

    public function clientIndexAction(Request $request, $cid){
        if (!$this->preAction($request, $cid, ['entitySettings' => false])) {
            return $this->responseAccessDenied();
        }
        $this->setTemplate('index');
        $this->setRenderOptions([
            'title' => $this->getTransHelper()->titleText('client_index'),
            'toolbars' => [
                $this->genToolbar('client_index')
            ],
            'table' => $this->genTable('index', null, [
                'actions' => 'index',
                'd' => [
                    'ajax' => [
                        'url' => $this->getUrl('ajax', null, true, [
                            "cid" => $cid
                        ]),
                        'dataSrc' => ''
                    ]
                ],            
                'filters' => $this->getFilters('table_client', [
                    'cid' => $cid
                ])
            ])
        ])
            ->addEntityModal();
        return $this->renderSystem();
    }

    public function packageAction(Request $request, $pid=0, $sid=0, $cid=0){
        if (!$this->preAction($request, $cid, ['checkPrivilages' => 1, 'entitySettings' => false])) {
            return $this->responseAccessDenied(true);
        }
        $this->entityQuery = null;
        $entities = $this->getEntiesFromBase($request, 'getEntities');
        $results = [];
        foreach ($entities as $entity) {
            $results[] = $entity->getShowData(false, ['type' => 'orders_package']);
        }
        //        if (count($this->entityActions) >0){
        //            $this->addEntitiesActions($entities, $this->entityActions);
        //        }
        return new JsonResponse($results);
    }
    
    public function doimportAction(Request $request, $cid = 0)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->responseMustAjax();
        }
        $messages = [];
        if (!$this->preAction($request, $cid, ['checkPrivilages' => 1, 'entitySettings' => false])) {
            return $this->responseAccessDenied(true);
        }
        $this->createImportForm();
        $this->formSystem->handleRequest($request);
        if ($this->formSystem->isValid()) {
            $import_options=$this->getImportOptions();
            $import_data=\json_decode($this->entity->content, true);
            $this->client=$this->entity->client;
            $imp=$this->importMedos($import_data, $import_options);
            $result=[
                'import' => $imp['import'],
                'messages' => [
                    'title' => $this->trans($this->messageText('import_report')),
                    'childs' => $imp['messages'],
                    'type' => 'success'
                ]
            ];
            if($imp['import']['ordersCount'] > 0){
                try {
                    $this->getEntityManager()->flush();
                    $confirm=$this->confirmMedos($imp);
                    $result['messages']['message']=$this->trans($this->messageText('import_success_count'), $imp['import']['ordersCount']);
                    $result['confirm']=$confirm;
                } catch (\Doctrine\ORM\ORMException $e) {
                    return $this->errorJsonResponse('base_error', $e->getMessage());
                } catch (\Exception $e) {
                    return $this->errorJsonResponse('server_error', $e->getMessage());
                }
            }else{
                $result['messages']['type']='error';
                $result['messages']['message']=$this->trans($this->messageText('import_empty'));
            }
            return $this->JsonResponse($result);
        }
        $this->setImportRenderOptions($request);
        return $this->errorsJsonResponse($messages);
    }

    public function importAction(Request $request, $cid = 0)
    {
        if (!$this->preAction($request, $cid, ['checkPrivilages' => 1])) {
            return $this->responseAccessDenied();
        }
        $this->createImportForm();
        $this->setImportRenderOptions($request);
        return $this->renderSystem(true);
    }
    
    
    // public function pdfAction(Request $request, $id, $cid=0){
    //     if (!$this->preAction($request, $cid)) {
    //         return $this->responseAccessDenied();
    //     }
    //     $modal=$request->query->get('type');
    //     $this->getEntityFromBase($id);
    //     $this->getEntitySettings();
    //     $this->setTemplate('pdf', null);
    //     $this->setRenderOptions([
    //         'title' => $this->getTransHelper()->titleText('show'),
    //         'entity'=>$this->entity->getShowData()
    //         //            'css_path'=> dirname($_SERVER['SCRIPT_FILENAME'])."/bundles/system/css/print.css"
    //     ]);

    //     $html=$this->renderSystemView();
    //     $options = [
    //         'title' => "zamówienie",
    //         'lowquality' => false,
    //         'dpi' => null,
    //         //            'orientation'  => "Landscape",
    //         'margin-top'    => 6,
    //         'margin-right'  => 6,
    //         'margin-bottom' => 10,
    //         'margin-left'   => 10,
    //     ];
    //     return new Response($this->get('knp_snappy.pdf')->getOutputFromHtml($html, $options),200,array('Content-Type'          => 'application/pdf','Content-Disposition'   => 'attachment; filename="file.pdf"'));
    //     return new Response($html, 200,['Content-Type' => 'text/html']);
    // }
    

    // public function getEntiesFromBase(Request $request, $functionName = null, $options = [])
    // {
    //     $filters = $request->query->get('f');
    //     $filters = isset($filters) ? json_decode($filters, true) : [];
    //     $defaultOptions = [
    //         'filters' => array_replace_recursive($this->entityQueryFilters, $filters)
    //     ];
    //     if (isset($this->entityQuery)) {
    //         $defaultOptions['query'] = $this->entityQuery;
    //     }
    //     $function = isset($functionName) ? $functionName : 'getAll';
    //     return $this->getEntityHelper()->getRepository()->$function(array_replace_recursive($defaultOptions, $options));
    // }

// </editor-fold>   
//  <editor-fold defaultstate="collapsed" desc="Import">  
    private function getImportOptions(){
        $params=['color', 'model', 'size', 'trims'];
        $options=[
        ];
        foreach($params as $p){
            $ns=$this->entityNameSpaces['nameSpace'];
            $dicName= Utils::deep_array_value( ['childs', $p], $ns::$shortNames ) ;           
            $options[$p]=[];
            foreach($this->entitySettings['dictionaries'][$dicName] as $dic_record){
                $options[$p][$dic_record['s']]=$dic_record['v'];
            }
        }
        foreach(['Orders', 'Positions'] as $n){
            $options[$n]['limits']=$this->getEntityHelper()->getSettingsValue('limits', $n);//Utils::deep_array_value('limits', $this->getEntitySettings($n), []);           
        }
        return $options;
    }
  
    private function createImportForm( $options = [])
    {
        if (!$this->entity) {
            $this->entity=new \stdClass();
            $this->entity->client=null;        
            $this->entity->type=1;
            $this->entity->content=null;
        }
        $this->formOptions = [
            'em' => $this->getDoctrine()->getManager(),
            'translator' => $this->get('translator'),
            'action' => $this->getUrl('doimport'),
            'attr' => [
                'data-admin' => $this->isAdmin(),
                'data-form' => 'ajax'
            ],
            'form_admin' => $this->isAdmin(),
            'method' => 'PUT',
            'client_choice' => !$this->isClient()
        ];
        Utils::addClass($this->formOptions, ['ajaxCarlack', static::en], 'attr');
        $this->formSystem = $this->createForm('AppBundle\\Form\\ImportOrdersType', $this->entity, $this->formOptions);
        
        // $this->setFormOptions('update', $options);
        // $this->setRenderOptions([
        //     'title' => $this->getTransHelper()->getTransHelper()->titleTex('edit'),
        //     'form_options' => [
        //         'submit' => $this->genSubmitBtn('update')
        //     ]
        // ]);
        // $this->createEntityForm();
        return $this;
    }   

    private function setImportRenderOptions($request){
        $this->setTemplate('import');
        $this->setRenderOptions([
            'template_body' => $this->entityNameSpaces['path'] . ':importForm.html.twig',
            'title' => $this->getTransHelper()->titleTex('import'),
            'form_options' => [
                'submit' => $this->genSubmitBtn('import')
            ]
            
        ]);
    }

// </editor-fold>   
//  <editor-fold defaultstate="collapsed" desc="Medos">  
    private function genImportMedosMessages(&$imp){
        $messages=[];
        foreach ($imp['deliveries'] as $delivery) {
            $dMsg=[
                'type' => $delivery['status'],
                'title' => $this->trans($this->messageText('import_delivery'), [$delivery['_purchaseMedosNumber']]),
                'message' => $delivery['messages'],
                'childs' => []
            ];
            foreach ($delivery['orders'] as $o) {
                $dOrder=[
                    'type' => $o['status'],
                    'title' => $this->trans($this->messageText('import_order'), [$o['clientOrderId']]),
                    'message' => $o['messages']
                ];
                if(array_key_exists('positions', $o) && count($o['positions']) ){
                    foreach ($o['positions'] as $pidx => $p) {
                        if(count($p['messages'])){
                            $dOrder['childs'][]=[
                                'type' => $p['status'],
                                'title' => $this->trans($this->messageText('import_position'), [$pidx+1]),
                                'message' => $p['messages']
                            ];
                        }

                    }
                }
                $dMsg['childs'][]=$dOrder;
                }
                $messages[]=$dMsg;
        }
        return $messages;
    }

    private function importMedos($medos=[], $options=[]){
        $options=array_replace_recursive($options, [ 
            'map_params' => [
                'model' => 'milling',
                'color' => 'colour',
                'size' => 'sillThick'
            ]
        ]);
        $dCount=count($medos);
        $imp=[
            'deliveries' => [],
            'count' => 0,
            'ordersCount' => 0
        ];
        foreach( $medos as $lp => $delivery){
            $d_data=[
                'id' => $delivery['id'],
                '_purchaseMedosNumber' => Utils::deep_array_value('_purchaseMedosNumber', $delivery, $delivery['id']),
                '_name' => Utils::deep_array_value('_name', $delivery),
                'generated' => Utils::deep_array_value('_generated', $delivery)
            ];
            $d_imp=\array_merge($d_data, [
                'status' => 'ok',
                'orders' => [],
                'messages'=> [],
                'count' => 0
            ]);
            if( array_key_exists('items', $delivery) && is_array($delivery['items']) && count($delivery['items']) > 0 ){
                $dOrders=[];
                $orders=[];
                foreach($delivery['items'] as $item ){//grupowanie po id zamówienia w medos (pole order)
                    if(!array_key_exists($item['order'], $dOrders)){
                        $dOrders[$item['order']]=[
                            'clientOrderId' => $item['order'],
                            'created' => $item['orderCreated'],
                            'delivery' => $d_data,
                            'items' => []
                        ];
                    }
                    $dOrders[$item['order']]['items'][]=$item;
                } 
                foreach($dOrders as $clientOrderId => $dOrder){//import zamówień (walidacja)
                    $o=$this->getEntityManager()->getRepository('AppBundle:Orders')->findOneBy(['client' => $this->client->getId(), 'clientOrderId' => $clientOrderId ]);
                    if(!$o){
                        $order=new Orders(['controller' => $this]);
                        $order->setClient($this->client);
                        $o_imp=$order->importMedos($dOrder, $options);
                        $d_imp['orders'][]=$o_imp;
                        switch ($o_imp['status']) {
                            case 'error' :
                                $d_imp['status'] = $o_imp['status'];
                            break;
                            case 'warning' :
                                if($d_imp['status'] != 'error'){
                                    $d_imp['status'] = $o_imp['status'];
                                }
                            default :
                                $this->getEntityManager()->persist($order);
                                $orders[]=$order;
                        }
                    }else{
                        if($d_imp['status']!='error'){
                            $d_imp['status'] = 'warning';
                            $msg=$this->trans($this->messageText('import_delivery_orders_exist'));
                            if(!in_array($msg, $d_imp['messages'])){
                                $d_imp['messages'][]=$msg;
                            }
                        }
                        $d_imp['orders'][]=[
                            'clientOrderId' => $clientOrderId,
                            'created' => $dOrder['created'],
                            'status' => 'warning',
                            'messages' => [$this->trans($this->messageText('import_order_exist'), [$o->getNumber()])],
                            'count' => 0
                        ];
                    }
                }
                $oCount=count($orders);
                $d_imp['count']=$oCount;
                $d_imp['messages'][] = $oCount > 0 ?  $this->trans($this->messageText('import_success_count'), $oCount) : $this->trans($this->messageText('import_empty'));
                $imp['count']++;
                $imp['ordersCount']+=$oCount;
            }else{
                $d_imp['status']='error';
                $d_imp['messages'][]=$this->trans($this->messageText('import_delivery_empty')).' ID:'.$d_data['id'].', NR:'.$d_data['_purchaseMedosNumber'];
            }            
            $imp['deliveries'][]=$d_imp;
        }   
        return [ 
            'import' => $imp,
            'messages' => $this->genImportMedosMessages($imp)
        ];

    }

    private function confirmMedos(){
        return [];
    }

// </editor-fold>   


}
