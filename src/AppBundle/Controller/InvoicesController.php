<?php
namespace AppBundle\Controller;

use AppBundle\Utils\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Invoices;
use AppBundle\Entity\Uploads;
use AppBundle\Entity\Positions;

class InvoicesController extends AppController
{

    const en = 'invoices';
    const ec = 'Invoices';
    

    public $ownTemplate = ['edit', 'Window/edit', 'show', 'Modal/show'];
    public $formExport = true;
    public static $importFields=['id', 'number', 'created'];
    
    public static function getFilters($type = 'index', $options = [])
    {
        $id = Utils::deep_array_value('id', $options);
        $cid = Utils::deep_array_value('cid', $options);
        $isClient=$cid != null;
        $filters = [];
        $fs = [
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
                // 'attr' => [
                //     'data-daterange' => json_encode([]),
                //     'data-filter-options' => json_encode(['type' => 'date_period']),
                // ],
                'd' => [
                    'filter-options' => json_encode(['type' => 'date_period']),
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
                    'filter-options' => json_encode(['type' => 'date_period']),
                    'widget' => 'daterange'
                ]
                
            ]
        ];
        
        switch ($type) {
            case 'index' :
                foreach (['client', 'created', 'term'] as $f) {
                    self::addFilter($filters, $fs[$f], $f);
                }
            break;
            case 'service' :
                foreach (['created', 'term'] as $f) {
                    self::addFilter($filters, $fs[$f], $f);
                }
            break;
            case 'table_client':
                self::addFilter($filters, self::genFilter('client_hidden', $options), 'client');
            break;
        }
        return $filters;
    }

    public static function getActions($type = 'view', $options=[])
    {
        $actions = [];
        $all = [
            'edit' => ['action' => 'edit', 'type' => 'w'],
            // 'show' =>  [ 'action' => 'show', 'type' => 'm', 'target' => 'invoices'],
            'copy' => [
                'action' => 'copy', 
                'browserAction' => true,
                'd' => [
                    'exp' => ''
                ]
            ],
            'show' => ['action' => 'show', 'browserAction' => true],
            'pdf' => [
                'action' => 'pdf', 
                'browserAction' => true,
                'd' => [
                    'exp' => 'order'
                ]
            ],
            // 'xls' => [
            //     'action' => 'xls', 
            //     'browserAction' => true,
            //     'd' => [
            //         'exp' => ''
            //     ]
            // ],
            'delete' => ['action' => 'delete', 'type' => 'm', 'target' => 'invoices']
        ];
        switch ($type) {
            case 'index' :
                $as = ['show', 'copy', 'pdf', 'edit', 'delete'];
            break;
            case 'view' :
            default :
                $as = ['show', 'copy', 'pdf'];
        }
        foreach ($as as $a) {
            $actions[$a] = $all[$a];
        }
        return $actions;
    }

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
            ]
        ];
        $btns=[];
        switch($type){
            case 'index':
                foreach(['new'] as $n){
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

    protected function customEditAction(Request $request, $id, $cid = 0)
    {
        $this->renderOptions['entity_data'] = $this->entity->getShowData();
    }
    
    protected function customNewEntity()
    {
        if ($this->isClient()) {
            $this->entity->setClient($this->client);        
        }
        return $this;
    }

    protected function setCustomFormOptions()
    {
        $this->formOptions['attr']['data-admin'] = $this->isAdmin();
        $this->formOptions['attr']['data-form'] = self::en;
        $this->formOptions['client_choice'] = ! ($this->isClient() || $this->entity->getClient());
        return $this;
    }

    
 // </editor-fold>   
    
 //  <editor-fold defaultstate="collapsed" desc="Actions">

    //    public function ajaxDataAction(Request $request, $id, $cid=0, $pid=0){
    //        $this->entityQuery="e.id, e.number as nr, e.clientNumber as cnr, DATE_FORMAT(e.generated, '%Y-%m-%d') as g, DATE_FORMAT(e.approved, '%Y-%m-%d') as ap, DATE_FORMAT(e.term, '%Y-%m-%d') as t, e.status as a_s, e.express as exp, e.quantity as q, e.area as a, e.progress as pr";
    //        return parent::ajaxDataAction($request, $id, $cid);
    //    }
    
    public function ajaxListToInvoiceAction(Request $request, $cid = 0){
        if (!$this->preAction($request, $cid, ['entitySettings' => false, 'checkPrivilages' => 1 ])) {
            return $this->responseAccessDenied(true);
        }
        // $this->setEntityQuery();
        return new JsonResponse($this->getEntiesFromBase($request, 'getListToInvoice'));
    }
 
// </editor-fold>   
//  <editor-fold defaultstate="collapsed" desc="Import">  

// </editor-fold>   
//  <editor-fold defaultstate="collapsed" desc="Medos">  


// </editor-fold>   


}
