<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Form\FormError;
use Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Utils\Utils;
use AppBundle\Utils\Filter;
use AppBundle\Entity\Settings;
use AppBundle\Entity\Uploads;
use AppBundle\Entity\Clients;

use AppBundle\Helpers\TransHelper;
use AppBundle\Helpers\EntityHelper;
use AppBundle\Helpers\SettingsHelper;
use AppBundle\Helpers\RouteHelper;
use AppBundle\Helpers\TemplateHelper;
use AppBundle\Helpers\FiltersGenerator;
use AppBundle\Helpers\FilterbarsGenerator;
use AppBundle\Helpers\ToolbarsGenerator;
use AppBundle\Helpers\DataTablesGenerator;
use AppBundle\Helpers\PanelsGenerator;
use AppBundle\Helpers\ModalsGenerator;

define("AppBundle", 'AppBundle');

class AppController extends Controller
{
    const en = 'clients';
    const ec = 'Clients';
    
    const emptyClientID = '__cid__';
    const emptyEntityID = '__id__';

    protected $clientRoute = false;

    public static $bundleName = AppBundle;// 'AppBundle';
    public static $bundlePath = AppBundle . ':';

    protected $settings = [];
    // protected $dictionaries = [];

    // protected $entitiesClasses = ['Clients', 'Settings', 'Uploads', 'Users', 'Notes', 'Colors', 'Models', 'Sizes', 'Trims', 'Orders', 'Positions', 'Products', 'Productions', 'Deliveries', 'Invoices', 'PriceLists', 'PriceListItems', 'Prices'];


    // protected $entityNameSpaces = [];

    protected $entityQuery;
    protected $entityQueryFilters = [];



    // protected $entitiesSettings = null;
    // protected $entitySettings = [];

    protected $entityModal = [];

    protected $comment = [
        'not_found' => '',
    ];
    protected $container;
    protected $user = null;
    protected $admin = false;
    protected $renderTemplate = '';
    protected $renderType='';
    protected $renderOptions = array(
        'template_body' => '',
        'template_errors' => 'form_errors.html.twig',
        'title' => '',
        'entity' => null,
        'entities_settings' => [],
        'ecn' => '',
        'en' => ''
    );
    protected $entityManager = null;
    protected $formOptions;
    protected $formSystem = null;
    protected $parameters;

    // protected static $activeFilter = [
    //     'name' => 'active',
    //     'data' => [
    //         ['v' => '1', 'n' => 'aktywny'],
    //         ['v' => '0', 'n' => 'nieaktywny']
    //     ],
    //     'd' => [
    //         'widget' => 'multiselect'
    //     ],
    //     'attr' => [
    //         'multiple' => 'multiple'
    //     ]

    // ];


    // protected static $activeHiddenFilter = [
    //     'name' => 'active',
    //     'type' => 'hidden',
    //     'value' => '1'
    // ];


    public function __construct($params = null)
    {
        if ($params) {
            if (isset($params['query'])) {
                $this->entityQuery = $params['query'];
            }

        }
    }

 //  <editor-fold defaultstate="collapsed" desc="Helpers">
    protected $th;// TransHelper
    protected $eh;//EntityHelper   
    protected $sh;//SettingsHelper
    protected $rh;//RouteHelper
    protected $tmplh;//TemplateHelper
    
    protected function getTransHelper():TransHelper
    {
        if(is_null($this->th)){
            $this->th=$this->get('helper.trans');
            $this->th->setEntityName(static::en);
        }
        return $this->th;
    }

    protected function trans($str, array $include=[]):string
    {
        return $this->getTransHelper()->trans($str, $include);
    }

    protected function getTemplateHelper():TemplateHelper
    {
        if(is_null($this->tmplh)){
            $this->tmplh=$this->get('helper.template');
        }
        return $this->tmplh;
    }

    protected function getEntityHelper():EntityHelper
    {
        if(is_null($this->eh)){
            $this->eh=$this->get('helper.entity');
            $this->eh->setEntityClassName(static::ec);
        }
        return $this->eh;
    }

    public function getSettingsHelper():SettingsHelper
    {
        if(is_null($this->sh)){
            $this->sh=$this->get('helper.settings');
        }
        return $this->sh;
    }
 
    public function getRouteHelper():RouteHelper
    {
        if(is_null($this->rh)){
            $this->rh=$this->get('helper.route');
        }
        return $this->rh;
    }

    public function getEntityManager()
    {
        if (!$this->entityManager) {
            $this->entityManager = $this->getDoctrine()->getManager();
        }
        return $this->entityManager;
    }

    public function controllerFunction( string $functionName, ?string $entityClassName=null, ?array $arguments = null)
    {
        $entityController=$this->getEntityHelper()->getControllerNamespace($entityClassName);
        if (method_exists($entityController, $functionName)) {
            return call_user_func_array([$entityController, $functionName], is_array($arguments) ? $arguments : [$arguments]);
        }
        return [];
    }
 
    public function runFunction($functionName, $argument = null)
    {
        if (method_exists($this, $functionName)) {
            return $argument ? $this->$functionName($argument) : $this->$functionName();
        }
        return null;
    }

    protected function getClientUrl(?string $routeSuffix = null, ?string $entityClassName = null, array $parameters = []):string
    {
        $def_param = [
            'cid' => is_null($this->client) ? self::emptyClientID : $this->client->getId()
        ];
        if ($this->entityId > 0) {
            $def_param['id'] = $this->entityId;
        }
        return $this->generateUrl(
            $this->getRouteHelper()->getClientRoute($routeSuffix, $entityClassName),
            array_replace_recursive($def_param, $parameters)
        );
    }

    protected function getEmployeeUrl(?string $routeSuffix = null, ?string $entityClassName = null, array $parameters = []):string
    {
        $def_param = [];
        if ($this->entityId > 0) {
            $def_param['id'] = $this->entityId;
        }
        return $this->generateUrl(
            $this->getRouteHelper()->getRoute($routeSuffix, $entityClassName),
            array_replace_recursive($def_param, $parameters)
        );
    }

    protected function getUrl(?string $routeSuffix = null, ?string $entityClassName = null, array $parameters = []):string
    {
        return $this->isClient() ? $this->getClientUrl($routeSuffix, $entityClassName, $parameters) : $this->getEmployeeUrl($routeSuffix, $entityClassName, $parameters);
    }
   
 // </editor-fold>  
 
 // <editor-fold defaultstate="collapsed" desc="Generators">
    protected $fbg;//FilterbarGenerator
    protected $fg;//FiltersGenerator
    protected $tbg;//ToolbarGenerator
    protected $dtg;// DataTablesGenerator
    protected $pg;// PanelsGenerator
    protected $mg;// ModalsGenerator

    protected function getFiltersGenerator():FiltersGenerator
    {
        if(is_null($this->fg)){
            $this->fg=$this->get('generator.filters');
        }
        return $this->fg;
    }

    protected function genFilters(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        return $this->getFiltersGenerator()->generate($type, $entityClassName, $options);
    }

    protected function getFilterbarsGenerator():FilterbarsGenerator
    {
        if(is_null($this->fbg)){
            $this->fbg=$this->get('generator.filterbars');
        }
        return $this->fbg;
    }

    protected function genFilterbar(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        return $this->getFilterbarsGenerator()->generate($type, $entityClassName, $options);
    }

    protected function getToolbarsGenerator():ToolbarsGenerator
    {
        if(is_null($this->tbg)){
            $this->tbg=$this->get('generator.toolbars');
        }
        return $this->tbg;
    }
    
    protected function genToolbar(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        return $this->getToolbarsGenerator()->generate($type, $entityClassName, $options);
    }

    protected function getPanelsGenerator():PanelsGenerator
    {
        if(is_null($this->pg)){
            $this->pg=$this->get('generator.panels');
        }
        return $this->pg;
    }
    
    protected function genPanel(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        return $this->getPanelsGenerator()->generate($type, $entityClassName, $options);
    }

    protected function getModalsGenerator():ModalsGenerator
    {
        if(is_null($this->mg)){
            $this->mg=$this->get('generator.modals');
        }
        return $this->mg;
    }
    
    protected function genModal(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        return $this->getModalsGenerator()->generate($type, $entityClassName, $options);
    }

    protected function getDataTablesGenerator():DataTablesGenerator
    {
        if(is_null($this->dtg)){
            $this->dtg=$this->get('generator.datatables');
        }
        return $this->dtg;
    }

    protected function genDT(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        return $this->getDataTablesGenerator()->generate($type, $entityClassName, $options);
    }

 // </editor-fold>  
 
 // <editor-fold defaultstate="collapsed" desc="Client">
 
    protected $client = null;
    
    protected function findClient($cid):?Clients
    {
        if ($cid > 0) {
            $this->client = $this->getEntityManager()->getRepository(Clients::class)->find($cid);
            if (!$this->client) {
                throw $this->createNotFoundException($this->trans(['message.error', 'clients.notFound']));
            }
            $this->renderOptions['client_name'] = $this->client->getName();
            $this->renderOptions['client_id'] = $this->client->getId();
        }
        return $this->client;
    }

    protected function getClientId():?int
    {
        return $this->isClient() ? $this->client->getId() : 0;
    }

 // </editor-fold>   

 // <editor-fold defaultstate="collapsed" desc="entity">
    protected $entity;
    protected $entityId=0;
  
    protected function newEntity(array $options = [] ):void
    {
        $ens=$this->getEntityHelper()->getEntityNamespace(static::ec);
        $this->entity = new $ens(array_replace_recursive( [
               'defaults' => $this->getEntityHelper()->getSettingsValue('defaults', static::ec)
            ],
            $options
        ));
    }

    protected function newEntityGenerator()
    {
        $this->entity=new \stdClass();
        $this->entity->items= new ArrayCollection();
    }

    public function fromBase($condition, ?string $entityClassName=null, bool $exeption=false){
        $repository = $this->getEntityManager()->getRepository($this->getEntityHelper()->getEntityNameSpace($entityClassName));
        $entity = is_array($condition) ? $repository->findOneBy($condition) : $repository->find($condition);
        if (!$entity && $exeption) {
            throw $this->createNotFoundException('NOT FOUND');
        }
        return $entity;
    }


    protected function getEntityFromBase(int $id, ?string $entityClassName = null)
    {
        $this->entity = $this->getEntityManager()->getRepository(
            $this->getEntityHelper()->getEntityNameSpace($entityClassName))->findOneById($id);
        if ($this->entity) {
            $this->entityId = $this->entity->getId();
        }
        return $this->entity;
    }
 // </editor-fold>   
 
 // <editor-fold defaultstate="collapsed" desc="Forms">
    protected function setFormOptions($type, $options = [])
    {
        $default = [
            'em' => $this->getDoctrine()->getManager(),
            'translator' => $this->get('translator'),
            'action' => $this->getUrl($type),
            'attr' => [
                'data-ecn' => static::ec,
                'data-admin' => $this->isAdmin(),
                'data-entity-id' => $this->entityId ? $this->entityId : '',
                'data-form' => 'ajax'
            ],
            'form_admin' => $this->isAdmin(),
        ];
        switch ($type) {
            case 'remove' :
                $default['method'] = 'DELETE';
                break;
            case 'update' :
                $default['method'] = 'PUT';
                break;
            case 'create' :
            case 'add':
            default :
                $default['method'] = 'POST';
        }
        if (Utils::deep_array_key_exists('attr-class', $this->formOptions) && Utils::deep_array_key_exists('attr-class', $options)) {
            Utils::addClass($this->formOptions, $options['attr']['class'], 'attr');
            unset($options['attr']['class']);
        }
        $this->formOptions = array_replace_recursive($this->formOptions, $default, $options);
        Utils::addClass($this->formOptions, ['ajaxCarlack', static::en], 'attr');
        if ($type != 'remove') {
            $this->formOptions['entities_settings'] = $this->getEntityHelper()->getEntitiesSettings();
            $this->runFunction('setCustomFormOptions');
        }
        return $this;
    }

    protected function createEntityForm()
    {
        $this->formSystem = $this->createForm($this->getEntityHelper()->getFormNamespace(), $this->entity, $this->formOptions);
        return $this;
    }

    protected function createCreateForm(array $options = [])
    {
        $this->setFormOptions('create', $options);
        $this->setRenderOptions([
            'title' => $this->getTransHelper()->titleText('new'),
            'form_options' => [
                'submit' => $this->genSubmitBtn('create')
            ]
        ]);
        $this->createEntityForm();
        return $this;
    }

    protected function createGenerateForm(array $options = [])
    {
        $this->newEntityGenerator();
        $this->setFormOptions('add', $options);
        $this->formOptions['attr']['data-form'] = static::en . 'generate';
        $this->setRenderOptions([
            'title' => $this->getTransHelper()->titleText('generate'),
            'template_body' => $this->getTemplate( 'generate_body', static::ec, false),
            'form_options' => [
                'submit' => $this->genSubmitBtn('save')
            ]
        ]);
        $this->formSystem = $this->createForm( $this->getEntityHelper()->getFormNamespace(static::ec . "Generate"), $this->entity, $this->formOptions);
        return $this;
    }

    protected function createEditForm(array $options = [])
    {
        $this->setFormOptions('update', $options);
        $this->setRenderOptions([
            'title' => $this->getTransHelper()->titleText('edit'),
            'form_options' => [
                'submit' => $this->genSubmitBtn('update')
            ]
        ]);
        $this->createEntityForm();
        return $this;
    }

    protected function createDeleteForm($id, $options = [])
    {
        $this->formData = new \stdClass();
        $this->formData->id = $id;
        $this->formData->confirm = false;
        $this->setFormOptions('remove');
        $renderOptions = [
            'title' => $this->getTransHelper()->titleText('delete'),
            'template_body' => $this->getTemplate('Form/delete'),
            'form_options' => [
                'submit' => $this->genSubmitBtn('remove')
            ],
            'entity_name' => static::en
        ];
        $renderOptions['entity'] = $this->entity->getDataDelete();
        $this->setRenderOptions($renderOptions);
        $this->formSystem = $this->createForm($this->getEntityHelper()->getFormNamespace('Delete'), $this->formData, $this->formOptions);
        return $this;
    }
 

 // </editor-fold>  

 //  <editor-fold defaultstate="collapsed" desc="Routing">

    protected function setReturnUrl()
    {
        $this->renderOptions['return_url'] = $this->getRequest()->headers->get('referer');
        return $this;
    }

 // </editor-fold>   
    
 //  <editor-fold defaultstate="collapsed" desc="Rendering">

    protected function getTemplate(string $name, ?string $entityClassName = null, bool $genericTemplate=true,  ?string $renderType = null):string
    {
        return $this->getTemplateHelper()->getPath($name, $entityClassName, $genericTemplate, $renderType );
    }
  

    protected function setTemplate(string $name, ?string $entityClassName = null, bool $genericTemplate=true )
    {
        $this->renderTemplate = $this->getTemplate($name, $entityClassName, $genericTemplate, $this->renderType );
        return $this;
    }

    protected function setRenderOptions($options = [])
    {
        $default = [
            'template_body' => $this->getTemplate( 'form', static::ec, false),
            'entity' => is_object($this->entity) ? $this->entity : null,
            'en' => static::en,
            'ecn' => static::ec,
            'admin' => $this->isAdmin(),
            'upload_route' => 'app_uploads_temp'
        ];
        $this->renderOptions = array_replace_recursive($this->renderOptions, $default, $options);
        $this->renderOptions['entities_settings'] = $this->getEntityHelper()->getEntitiesSettings();
        $this->renderOptions['layout'] = Utils::deep_array_value('view', $_REQUEST) ? 'content' : 'layout_dev';
        return $this;
    }

    protected function renderSystem($genFormView = false)
    {
        if ($genFormView) {
            $this->renderOptions['form'] = $this->formSystem->createView();
        }
        return $this->render($this->renderTemplate, $this->renderOptions);
    }

    protected function renderSystemView($genFormView = false)
    {
        if ($genFormView) {
            $this->renderOptions['form'] = $this->formSystem->createView();
        }
        return $this->renderView($this->renderTemplate, $this->renderOptions);
    }

// </editor-fold>   

// <editor-fold defaultstate="collapsed" desc="Actions">

    public function getRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }

    public function getEntityFieldFromJson(Request $request, $fieldName="id")
    {
        $idArray=[];
        $content=$request->getContent();
        if (!empty($content))
        {
            $enties = json_decode($content, true);
            foreach ($enties as $entity) {
                $idArray[]=$entity[$fieldName];
            }
        }
        return $idArray;
    }

    public function loginRedirectAction(Request $request)
    {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            return $this->redirectToRoute('login');
        }

        if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))
        {
            return $this->redirectToRoute('app_admin');
        }
        else if($this->get('security.authorization_checker')->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('app_employee');
        }
        else if($this->get('security.authorization_checker')->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('app_client');
        }
        else
        {
            return $this->redirectToRoute('login');
        }

    }

    protected function preAjaxAction(Request $request, $cid = 0, $privileges = true):bool
    {
        
        if ($cid == 0) {
            $cid = $request->query->get('cid');
        }
        if ($cid != 0) {
            $this->findClient($cid);
        }
        if (!$this->checkPrivilages($privileges)) {
            return false;
        }
        return true;
    }

    protected function preAction(Request $request, $cid = 0, array $options = []):bool
    {
        $default = [
            'checkPrivilages' => 0,
            'entitySettings' => true,
        ];
        $this->renderType = $request->query->get('type');        
        $o = array_replace_recursive($default, $options);
        if ($cid == 0) {
            $cid = $request->query->get('cid');
        }
        if ($cid != 0) {
            $this->findClient($cid);
        }
        if ($o["checkPrivilages"] >= 0 && !$this->checkPrivilages($o["checkPrivilages"] == 1)) {
            return false;
        }
        if ($o['entitySettings']) {
            $this->entitySettings = $this->getEntityHelper()->getSettings();
        }
        return true;
    }

    public function clientIndexAction(Request $request, $cid){
        if (!$this->preAction($request, $cid)) {
            return $this->responseAccessDenied();
        }
        $this->setTemplate('index');
        $this->setRenderOptions([
            'title' => $this->getTransHelper()->titleTex('client_index'),
            'toolbars' => [
                $this->genToolbar('client_index', static::ec, [
                    "clientId" => $this->getClientId()
                ]),
                $this->genFilterbar('client_index')
            ],
            'table' => $this->genDT('client_index', static::ec, [ 
                'clientId' => $this->getClienId(),
                'actions' => 'client_index',
                'ajax' =>[
                    'filtersType' => 'table_client'
                ]
            ])
        ])
            ->addEntityModal();
        return $this->renderSystem();
    }


    public function indexAction(Request $request, $cid = 0)
    {
        if (!$this->preAction($request, $cid)) {
            return $this->responseAccessDenied();
        }
        $this->setTemplate('index');
        $this->setRenderOptions([
            'title' => $this->getTransHelper()->titleText('index'),
            'toolbars' => [
                $this->genToolbar(),
                $this->genFilterbar()
            ],
            'table' => $this->genDT()
        ])
            ->addEntityModal();
        return $this->renderSystem();
    }

    public function ajaxTestAction(Request $request)
    {
        $queryStr=$request->query->get('q');
        $queryStr="SELECT e.id, e.generated, COUNT(pg.id) - SIZE(e.positions) as pg_count, c.name
        FROM AppBundle\Entity\Orders e 
        JOIN e.positions p 
        JOIN p.package pg 
        JOIN p.size s
        join e.client c
        
        WHERE e.generated > '2017-01-01' 
        GROUP BY e
        ORDER BY e.id ASC";

        $queryStr="SELECT e FROM AppBundle\Entity\Orders e 
        LEFT JOIN e.client e_client 
        LEFT JOIN e_client.ways e_client_ways 
        LEFT JOIN e.positions e_positions 
        WHERE e_client_ways.id IN ('4') AND (e.approved BETWEEN '2015-05-14 00:00:00' AND '2017-11-14 23:59:59') AND e_positions.package IS NULL AND e.actualState NOT IN(1, 5) AND e_positions.size = 1 GROUP BY e.id";

        $queryStr="SELECT e.id AS id, partial e_client.{id, name, code}, e.number AS nr 
        FROM AppBundle\Entity\Orders e 
        LEFT JOIN e.client e_client";

        $queryStr="SELECT partial e.{id as id, number as nr} , partial e_client.{id, name, code} 
        FROM AppBundle\Entity\Orders e 
        LEFT JOIN e.client e_client";

        $query=$this->getEntityManager()->createQuery($queryStr);
        $result= $query->getArrayResult();
       
        return new JsonResponse($result);
    }

    public function ajaxListAction(Request $request, $cid = 0)
    {
        if (!$this->preAjaxAction($request, $cid)) {
            return $this->responseAccessDenied(true);
        }
        return new JsonResponse($this->getEntiesFromBase($request, 'getList'));
    }

    public function clientAjaxListAction(Request $request, $cid = 0)
    {
        if (!$this->preAjaxAction($request, $cid)){
            return $this->responseAccessDenied(true);
        }
        // $defaultFilters=$this->getFiltersGenerator()->generate('table_client', static::ec, [ 'values' => [ 'client' => $this->getClientId() ]  ]);
      
        $filters = $request->query->get('f');
        $clientFilter=$this->getFiltersGenerator()->generateClientFilter(static::ec, $this->getClientId());
        $defaultFilters= count($clientFilter) > 0 ?  ['client' => $clientFilter ] :[] ;
        return new JsonResponse($this->getEntiesFromBase($request, 'getList', [ 
            'filters' => isset($filters) ? array_replace_recursive($defaultFilters, json_decode($filters, true)) : $defaultFilters 
            // 'filters' => is_set($filters) ? json_decode($filters, true) : [] 
            ])
        );
    }

    public function ajaxDicAction(Request $request, $cid = 0)
    {
        if (!$this->preAction($request, $cid, ['entitySettings' => false])) {
            return $this->responseAccessDenied(true);
        }
        return new JsonResponse($this->getEntiesFromBase($request, 'getDic'));
    }

    public function ajaxListDetailsAction(Request $request, $cid = 0)
    {
        if (!$this->preAction($request, $cid, ['entitySettings' => false])) {
            return $this->responseAccessDenied(true);
        }
        $this->entityQuery = null;
        $entities = $this->getEntiesFromBase($request, 'getEntities');
        $results = [];
        foreach ($entities as $entity) {
            $results[] = $entity->getShowData([ 
                'type' => 'details',
                'shortNames' => true
            ]);
        }
        return new JsonResponse($results);
    }

    public function ajaxDataAction(Request $request, $id, $cid = 0)
    {
        if (!$this->preAction($request, $cid, ['entitySettings' => false])) {
            return $this->responseAccessDenied(true);
        }
        $this->getEntityFromBase($id);
        return new JsonResponse(['entity' => $this->entity->getShowData()]);
    }

    public function showAction(Request $request, $id, $cid = 0)
    {
        if (!$this->preAction($request, $cid)) {
            return $this->responseAccessDenied();
        }
        $this->getEntityFromBase($id);
        $this->setTemplate('show')
            ->setRenderOptions([
            'title' => $this->getTransHelper()->titleText('show'),
            'entity' => $this->entity->getShowData(['shortNames' => false])
        ]);
        return $this->renderSystem();
    }

    public function newAction(Request $request, $cid = 0)
    {
        if (!$this->preAction($request, $cid)) {
            return $this->responseAccessDenied();
        }
        $this->newEntity();
        $this->setTemplate('edit');
        $this->createCreateForm();
        if (method_exists($this, 'customNewAction')) {
            $this->customNewAction($request, $cid);
        }
        return $this->renderSystem(true);
    }

    public function createAction(Request $request, $cid = 0)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->responseMustAjax();
        }
        $dataReturn = [];
        if (!$this->preAction($request, $cid)) {
            return $this->responseAccessDenied(true);
        }
        $this->newEntity();
        $this
            ->createCreateForm()
            ->formSystem->handleRequest($request);
        if ($this->formSystem->isValid()) {
            // if (property_exists($this->entity, 'upload')) {
            //     $this->entity->checkUpload();
            // }
            if (method_exists($this, 'customCreateAction')) {
                $this->customCreateAction($dataReturn);
            }
            return $this->responseSave('create', $dataReturn);
        }
        return $this->errorsJsonResponse( 'create',  $dataReturn);
    }

    public function generateAction(Request $request, $cid=0){
        if (!$this->preAction($request, $cid, ['checkPrivilages' => 1, 'entitySettings' => false])) {
            return $this->responseAccessDenied(true);
        }            
        $this->setTemplate('generate');
        $this->createGenerateForm(
            [
                'attr' => [
                    'data-uniques' =>  json_encode($this->getEntityManager()->getRepository($this->entityNameSpaces['path'])->getUniques())
                ]
            ]
        );
        if (method_exists($this, 'customGenerateAction')) {
            $this->customGenerateAction( $request, $cid);
        }
        return $this->renderSystem(true);
    }

    public function addAction(Request $request, $cid = 0)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->responseMustAjax();
        }
        $dataReturn = [];
        if (!$this->preAction($request, $cid)) {
            return $this->responseAccessDenied(true);
        }
        $this
            ->createGenerateForm()
            ->formSystem->handleRequest($request);
        if ($this->formSystem->isValid()) {
            $dataReturn['count']=$this->entity->items->count();
            $uniques =  $this->getEntityManager()->getRepository($this->entityNameSpaces['path'])->getUniques();
            foreach($this->entity->items as $item){
                if (method_exists($item, 'getUnique') && (array_key_exists($item->getUnique(), $uniques))){
                    $this->entity->items->removeElement($item);
                }
            }
            if (method_exists($this, 'customAddAction')) {
                $this->customAddAction( $dataReturn);
            }           
            return $this->responseSaveMany($this->entity->items, 'add', $dataReturn);
        }
        return $this->errorsJsonResponse( 'add',  $dataReturn);
    }
    

    public function editAction(Request $request, int $id, int $cid = 0)
    {
        if (!$this->preAction($request, $cid)) {
            return $this->responseAccessDenied();
        }
        $this->getEntityFromBase($id);
        $this->createEditForm();
        $this->setTemplate('edit');
        $this->customEditAction($request, $id, $cid);
        return $this->renderSystem(true);
    }

    protected function customEditAction(Request $request, int $id, int $cid = 0):void
    {
    }

    public function updateAction(Request $request, int $id, int $cid = 0)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->responseMustAjax();
        }
        if (!$this->preAction($request, $cid)) {
            return false;
        }
        $this->getEntityFromBase($id);
        $this->entity->preUpdate();
        $this->createEditForm();
        $this->preUpdateAction($request, $id, $cid);
        $dataReturn = [];
        $this->formSystem->handleRequest($request);
        if ($this->formSystem->isValid()) {
            // $this->entity->postUpdate($this->getEntityManager());
            $this->customUpdateAction($dataReturn);
            return $this->responseSave('update', $dataReturn);
        }
        return $this->errorsJsonResponse('update', $dataReturn);
    }

    protected function preUpdateAction(Request $request, int $id, int $cid = 0):void
    {
    }

    protected function customUpdateAction(array &$dataReturn):void
    {       
    }

    public function deleteAction(Request $request, $id, $cid = 0)
    {
        if (!$this->preAction($request, $cid)) {
            return $this->responseAccessDenied();
        }
        $this->getEntityFromBase($id);
        $this->setTemplate('edit');
        $this->createDeleteForm($id);
        return $this->renderSystem(true);
    }

    public function removeAction(Request $request, $id, $cid = 0)
    {
        if (!$this->preAction($request, $cid)) {
            return $this->responseAccessDenied(true);
        }
        $dataReturn = [];
        if ($this->getEntityFromBase($id)) {
            $this->createDeleteForm($id);
            $this->formSystem->handleRequest($request);
            if ($this->formSystem->isValid()) {
                if ($this->formData->confirm) {
                    $dataReturn=$this->entity->getSuccessData('remove');
                    $this->customRemoveAction($dataReturn);
                    return $this->responseSave('remove', $dataReturn, 'remove');
                }else{
                    $dataReturn['errors']['childs'][]=$this->errorMessage(['message' => 'not_confirmed']);
                }
            }
        }else{
            $dataReturn['errors']['childs'][]=$this->errorMessage(['message' => 'not_found']);
        }
        return $this->errorsJsonResponse('remove', $dataReturn);
    }

    protected function customRemoveAction(array &$dataReturn):void
    {       
    }

// </editor-fold>


//  <editor-fold defaultstate="collapsed" desc="Parameters">
    
    protected function getParameters()
    {
        if (!$this->parameters) {
            $this->parameters = $this->container->getParameter('app');
            // $this->positionParameters=$this->container->getParameter('_system.position');
            $this->renderOptions['parameters'] = json_encode($this->parameters);
        }
        return $this->parameters;
    }
    
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Utilites">
    

    public function isAdmin()
    {
        $this->admin = $this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        $this->formOptions['form_admin'] = $this->admin;
        return $this->admin;
    }

    public function isClient()
    {
        return ($this->client != null);
    }

    

    public function checkPrivilages($onlyAdmin = true)
    {
        $this->user = $this->container->get('security.token_storage')->getToken()->getUser();
        return (($onlyAdmin && $this->isAdmin()) || (!$onlyAdmin && ($this->isAdmin() || ($this->isClient() && $this->user->hasClient($this->client))) ));
    }


    public function addEntityModal(?string $type=null, ?string $entityClassName=null, array $options = [])
    {
        $this->renderOptions['modals'][]=$this->getModalsGenerator()->generateEntityModal($type, $entityClassName, $options);
        return $this;
    }
  
    public function addEntityModal1(?string $entityClassName=null, array $options = []):array
    {
        $en = $this->getEntityHelper()->getEntityName($entityClassName); 
        $default = [
            'name' => $en,
            'en' => $en,
            'ecn' => $entityClassName,
            'd' => [],
            'attr' => [
                'class' => 'ajax'
            ]
        ];
        $settings = $this->controllerFunction('getModal', $entityClassName);
        return $this->addModal(array_replace_recursive($default, $settings, $options));
    }

    public function addModal(?string $type=null, array $options=[])
    {
        $this->renderOptions['modals'][]=$this->getModalsGenerator()->generate($type, '', $options);
        return $this;
    }

    
    public function addModal1($modal)
    {
        $name = $modal['name'];
        $ecn = Utils::deep_array_value('ecn', $modal, '');
        $en = Utils::deep_array_value('en', $modal);
        if(is_null($en) && $ecn ){
            $en=$this->getEntityName($ecn);
        }
        $fillData = Utils::deep_array_value('fillData', $modal, false);
        $default = [
            'title' => $this->getTransHelper()->modalTitle($name, $en),
            'd' => [
                'method' => 'POST',
                'options' => [
                    'ecn' => $ecn,
                    'en' => $en
                ]
            ],
            'attr' => [
                'id' => $name . '_modal'
            ]
        ];
        if ($fillData && !array_key_exists('data', $modal)) {
            $default['data'] = $ecn != '' ? $this->getEntityManager()->getRepository('AppBundle:' . $ecn)->getList() : null;
        }
        if (!array_key_exists('settings', $modal)) {
            $default['settings'] = $ecn ? $this->getEntityHelper()->getSettings($ecn) : $this->getSettingsHelper()->getSettingValue('modal-'.$name);
        }
        $this->renderOptions['modals'][] = array_replace_recursive($default, $modal);
        return $this;
    }

    protected function customModalField($modal)
    {
        $ecn = Utils::deep_array_value('ecn', $modal);
        if (\array_key_exists('dic', $modal)) {
            if (!is_array(Utils::deep_array_value('data', $modal))) {
                $modal['data'] = $this->getEntityHelper()->getDic(is_string($modal['dic']) ? $modal['dic'] : $ecn);
            }
            $imgWidth = Utils::deep_array_value('image-width', $this->getEntityHelper()->getSettings($ecn));
            if ($imgWidth) {
                $imgColumns = Utils::deep_array_value('image-columns', $this->getEntityHelper()->getSettings($ecn), intval(600 / $imgWidth));
                $modal['dialog_attr']['style'] = Utils::deep_array_value('dialog_attr-style', $modal, '') . 'max-width:' . ($imgColumns * $imgWidth + 100) . 'px;';
            }
        }
        return $modal;
    }

    protected function addModalsField1($modals)
    {
        $default = [
            'content' => $this->getTemplate('field', static::ec, true, 'm'),
            'attr' => [
                'class' => 'modal-field',
            ],
            'd' => [
                'set-focus' => '.field'
            ]
        ];
        foreach ($modals as $modal) {
            $this->addModal(array_replace_recursive($default, $this->customModalField($modal)));
        }
        return $this;
    }

    protected function addModalsField($fields)
    {
        $mo = $this->getModalsGenerator()->generateFieldModals($fields);
        foreach ($mo as $modal) {
            $this->renderOptions['modals'][]=$modal;
        }
        return $this;
    }

    protected function addExpModal(?string $entityClassName = null, array $options = [])
    {
        $this->renderOptions['modals'][]=$this->getModalsGenerator()->generateExpModal($entityClassName, $options);
        return $this;
    }

    protected function addExpModal1(?string $entityClassName = null, array $options = [])
    {
        $ec = $this->getEntityHelper()->getEntityClassName($entityClassName);
        $en = $this->getEntityHelper()->getEntityName($entityClassName);
        $this->addModalsField([
            array_replace_recursive(
                [
                    'ecn' => $ec,
                    'name' => $en . '_exp',
                    'fieldtype' => 'textarea',
                    'field_attr' => [
                        'id' => $en . '_exp_copy',
                        'rows' => 10,
                        'cols' => 25,
                        'data-widget' => 'copytextarea',
                    ],
                    'showSave' => false,
                    'd' => [
                        'set-focus' => ''
                    ]
                ],
                $options
            )
        ]);
        return $this;
    }

    public function addShowModal($entityClassName = null, $options = [])
    {
        $ens = $this->getEntityNameSpaces($entityClassName);
        $modal = $this->controllerFunction('getModal', $entityClassName);
        $modal['name'] = $ens['name'] . '_show';
        $modal['content'] = $this->getTemplate( 'show', static::ec, false, 'm');
        $modal['addClass'][] = 'modal-show';
        $this->addModal(array_replace_recursive($modal, $options));
        return $this;
    }

    public function addTableExportModal($entityClassName = null, $options = [])
    {
        // $ens = $this->getEntityNameSpaces($entityClassName);
        $en=$this->getEntityHelper()->getEntityName($entityClassName);
        // $modal = $this->controllerFunction('getModal', $entityClassName);
        $modal['en'] = $en;
        $modal['ecn'] = $this->getEntityHelper()->getEntityClassName($entityClassName);
        $modal['name'] = $en . '_table_export';
        $modal['content'] = $this->getTemplate( 'tableExport', static::ec, true, 'm' );
        $modal['addClass'][] = 'modal-table-export';
        $this->addModal(array_replace_recursive($modal, $options));
        return $this;
    }
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Database">

    public function getEntiesFromBase(Request $request, $functionName = null, $options = [])
    {
        $filters = $request->query->get('f');
        $order = $request->query->get('o');
        $filters = isset($filters) ? json_decode($filters, true) : [];
        $defaultOptions = [
            'filters' => array_replace_recursive($this->entityQueryFilters, $filters)
        ];
        if(isset($order)){
            $defaultOptions['order'] = json_decode($order, true);
        }
        if (isset($this->entityQuery)) {
            $defaultOptions['query'] = $this->entityQuery;
        }
        $function = isset($functionName) ? $functionName : 'getAll';
        return $this->getEntityHelper()->getRepository()->$function(array_replace_recursive($defaultOptions, $options));
        // getEntityManager()->getRepository($this->entityNameSpaces['path'])->$function(array_replace_recursive($defaultOptions, $options));
    }

    public function getEntityCount($entityClassName = null)
    {
        $ens = $this->getEntityNameSpaces($entityClassName);
        return $this->getEntityManager()->getRepository($ens['path'])->getCount();
    }

    
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Generate functions">

    protected function genElement(string $name, ?string $entityClassName=null):array
    {
        $en=$this->getEntityHelper()->getEntityName($entityClassName);
        return [
            'name' => $en,
            'en' => $en,
            'ecn' => $this->getEntityHelper()->getEntityClassName($entityClassName),
            'attr' => [
                'id' => $en . '_' . $name
            ]
        ];
    }

    public function genSubmitBtn($type):array
    {
        return [
            'attr' => [
                'value' => $type,
                'title' => $this->getTransHelper()->btnTitle($type),
                'class' => $type == 'remove' ? 'btn-danger' : 'btn-success',
                'style' => $type == 'remove' ? "disabled:true" : ""
            ]
        ];
    }

    // protected function genPanel(?string $entityClassName = null, array $options = []):array
    // {
    //     return array_replace_recursive(
    //         $this->genElement('panel', $entityClassName),
    //         
    //     );
    // }

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Messages">
    public function responseMessage(array $msg, $entityName = null, bool $translate = true, array $data=[]):array
    {
        Utils::deep_array_value_set('type', $msg, 'info');
        if ($translate) {
            $label = Utils::deep_array_value('label', $msg, '');
            $msg['label'] = $this->trans($this->getTransHelper()->labelText($label, $entityName));
            $msg['title'] = $this->trans($this->getTransHelper()->titleText(Utils::deep_array_value('title', $msg), $entityName));
            $message=Utils::deep_array_value('message', $msg, $label);
            if(is_array($message)){
                $msg['message']=[];
                foreach($message as $m){
                    $msg['message'][] = $this->trans($this->getTransHelper()->messageText($m, $entityName), $data);
                }
            }else{
                $msg['message'] = $this->trans($this->getTransHelper()->messageText($message, $entityName), $data);
            }
        }
        return $msg;
    }

    protected function customMessages(&$messages, $type){
        return $messages;
    }

    protected function getMessageData( $type, $dataReturn=[]){
        $data=[];
        switch ($type){
            case 'add':
                $data=[$dataReturn['added'], $dataReturn['count']];
            break;
            default:
                if($this->entity){
                    $data=$this->entity->getMessageData($type, $dataReturn);
                }
        }
        return $data;
    }

    public function errorMessage(array $msg, ?string $entityName = null, bool $translate = true){
        $msg['type']='error';
        return $this->responseMessage($msg, $entityName, $translate);
    }

    private function getFormErrorsMessages(){
        $errors=$this->formSystem ? $this->formSystem->getErrors(true) : [];
        if(count($errors)){
            $messages=$this->errorMessage([ 'title' => 'error.form' ], '');
            $messages['childs']=[];
            foreach ($errors as $key => $error) {
                $messages['childs'][] = $this->errorMessage([
                    'label' =>  $this->trans($this->getTransHelper()($error->getOrigin()->getConfig()->getOption('label'), null )),
                    'message' => $error->getMessage()
                ], null, false);
            }
            return $messages;
        }
        return null;
    }

// </editor-fold>
    
// <editor-fold defaultstate="collapsed" desc="Response">
    
    public function JsonResponse($dataReturn = [], $success = true)
    {
        return new JsonResponse($dataReturn, $success ? 200 : 400);
    }
   
    public function saveManyToBaseJson($entities, $type, $dataReturn = [])
    {
        try {
            $this->getEntityManager()->flush();
            $msg=$this->responseMessage([
                'title' => $type,
                'message' => $type,
                'type' => 'success'
            ], null, true, $this->getMessageData($type, $dataReturn));
            $dataReturn = array_merge_recursive(
                $dataReturn, 
                [
                    "messages" => $this->customMessages($msg, $type),
                    "entities" => []
                ]);

            foreach($entities as $entity){
                $dataReturn['entities'][]=$entity->getSuccessData($type);
            }
            return new JsonResponse($dataReturn, 200);
        } catch (\Doctrine\ORM\ORMException $e) {
            return $this->errorJsonResponse('base', $e->getMessage());
        } catch (\Exception $e) {
            return $this->errorJsonResponse('server', $e->getMessage());
        }
    }

    public function saveToBaseJson($type, $dataReturn = [])
    {
        try {
            $this->getEntityManager()->flush();
            if ($this->entity ) {
                $this->entityId = $this->entity->getId();
                $msg=$this->responseMessage([
                    'title' => $type,
                    'message' => $type,
                    'type' => 'success'
                ], null, true, $this->getMessageData($type, $dataReturn));

                $dataReturn = array_merge_recursive(
                    $dataReturn, 
                    [
                        "id" => $this->entityId,
                        "messages" => $this->customMessages($msg, $type)
                    ],
                    $this->entity->getSuccessData($type)
                );
                $fnPost='post'.\ucfirst($type).'Action';
                if (method_exists($this, $fnPost)) {
                    $this->$fnPost($dataReturn);
                }
                if ($this->entityId) {
                    $dataReturn['edit_param'] = [
                        'title' => $this->trans($this->getTransHelper()->titleText('edit')),
                        'urls' => [
                            'site' => $this->getUrl('edit'),
                            'form' => $this->getUrl('update')
                        ],
                        'submit' => [
                            'label' => $this->trans($this->getTransHelper()->labelText('update', '')),
                            'title' => $this->trans($this->getTransHelper()->titleText('update', '')),
                        ]
                    ];
                }
            }
            return new JsonResponse($dataReturn, 200);
        } catch (\Doctrine\ORM\ORMException $e) {
            return $this->errorJsonResponse('base', $e->getMessage());
        } catch (\Exception $e) {
            return $this->errorJsonResponse('server', $e->getMessage());
        }
    }

    public function responseSave($type, $dataReturn=[], $command="persist"){
        if (is_null($dataReturn) || (is_array($dataReturn) && !array_key_exists('errors', $dataReturn))) {
            $this->getEntityManager()->$command($this->entity);
            return $this->saveToBaseJson($type, $dataReturn);
        }
        return $this->errorsJsonResponse($type,  $dataReturn);
    }

    public function responseSaveMany($entities, $type, $dataReturn=[], $command="persist"){
        if (is_null($dataReturn) || (is_array($dataReturn) && !array_key_exists('errors', $dataReturn))) {
            $dataReturn['added'] = 0;
            Utils::deep_array_value_set('count', $dataReturn, $entities->count());
            foreach($entities as $entity){
                $this->getEntityManager()->$command($entity);
                $dataReturn['added']++;
            }

            return $this->saveManyToBaseJson($entities, $type, $dataReturn);
        }
        return $this->errorsJsonResponse($type,  $dataReturn);
    }

    public function errorJsonResponse($type='', $messageStr = '', $entityClassName='', $translate=false)
    {
        $msg=$this->errorMessage([ 'title' => 'error.'.$type ], $entityClassName, true);
        $msg['message']= $translate ? $this->trans($this->getTransHelper()->messageText($messageStr, $entityClassName)) : $messageStr;
        return new JsonResponse( ['errors' => $msg ], 400 );
    }

    public function responseMustAjax()
    {
        return $this->errorJsonResponse('server', 'must_ajax', '', true);
    }

    public function responseAccessDenied($ajax=false)
    {
        throw $this->createAccessDeniedException();
        if($ajax){
            return $this->errorJsonResponse('server', 'access_denied', '', true);
        }
        $this->setTemplate('accessDenied');
        $this->setRenderOptions([
            'title' => $this->titleText('error.system', '')
        ]);
        return $this->renderSystem();
    }

    public function errorsJsonResponse($type, $dataReturn = [])
    {
        if(is_array($formErrors=$this->getFormErrorsMessages())){
            if(!is_array(Utils::deep_array_value('errors-childs', $dataReturn))){
                $dataReturn['errors']['childs']=[];
            }
            $dataReturn['errors']['childs'][]=$formErrors;
        }
        if($this->formSystem){
        if (is_object($this->entity)) {
            $this->renderOptions['entity'] = $this->entity;
        }
        $this->renderOptions['form'] = $this->formSystem->createView();
        $dataReturn['form_body'] = $this->renderView($this->renderOptions['template_body'], $this->renderOptions);
        }
        return new JsonResponse(array_merge_recursive([ 
                'errors' => $this->errorMessage([ 'title' => 'error.'.$type])
            ],
            $dataReturn
        ), 400);
    }

// </editor-fold>



}
