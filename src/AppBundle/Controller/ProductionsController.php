<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Controller\AppController;
use AppBundle\Utils\Utils;

/**
 * Productions controller.
 *
 */
class ProductionsController extends AppController
{
    const en = 'productions';
    const ec = 'Productions';

    public static function getFilters($type = 'index', $options = [])
    {
        $id = Utils::deep_array_value('id', $options);
        $cid = Utils::deep_array_value('cid', $options);
        $isClient = $cid != null;
        $filters = [];
        $fs = [
            'generated' => [
                'name' => 'generated',
                'type' => 'input',
                'label' => 'productions.label.generated',
                'setValue' => [
                    'type' => 'settings',
                    'query' => 'productions-filters-generated-value'
                ],
                'source' => [
                    'type' => 'settings',
                    'query' => 'productions-filters-dateRanges'
                ],
                'd' => [
                    'filter-options' => json_encode(['type' => 'date_period']),
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
                    'filter-options' => json_encode(['type' => 'between']),
                    'widget' => 'combobox'
                ]
            ]
        ];
        switch ($type) {
            case 'index':
                foreach (['generated', 'progress'] as $f) {
                    $filters[] = $fs[$f];
                }
            break;
        }
        return $filters;
    }

    public static function getActions($type = 'view', $options = [])
    {
        $actions = [];
        $all = [
            'edit' => ['action' => 'edit', 'type' => 'w'],
            'show' => ['action' => 'show', 'browserAction' => true],
            'xls' => [
                'action' => 'xls',
                'browserAction' => true,
                'd' => [
                    'exp' => ''
                ]
            ],
            'delete' => ['action' => 'delete', 'type' => 'm', 'target' => 'productions']
        ];
        switch ($type) {
            case 'index':
                $as = ['show', 'xls',  'edit', 'delete'];
            break;
            case 'view':
            default:
                $as = ['show', ];
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
                'attr' => [
                    'class' => 'btn-primary',
                    'target' => '_blank'
                ],
                'routeParam' => [ 'type' => 'w' ]
            ]
        ];
        $btns=[];
        switch($type){
            case 'index':
                foreach(['new' ] as $n){
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
    //  <editor-fold defaultstate="collapsed" desc="Custom functions">
    protected function customMessages(&$messages, $type)
    {
        switch ($type) {
            case 'create':
            case 'update':
                $messages['message']=$this->trans($this->messageText($type), [$this->entity->getNumber()]);
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

    protected function customUpdateAction(&$dataReturn)
    {
        $em = $this->getEntityManager();
        $removedProducts = $this->entity->getRemovedProducts();
        if (!is_null($removedProducts)) {
            foreach ($removedProducts as $p) {
                $em->remove($p);
            }
        }
        return $dataReturn;
    }

    protected function setCustomFormOptions()
    {
        $this->formOptions['attr']['data-admin'] = $this->isAdmin();
        $this->formOptions['attr']['data-form'] = self::en;
        $this->formOptions['attr']['style'] = "min-width: 1200px;";
        $pns = $this->getEntityNameSpaces('Products');
        $controller = self::getEntityController($pns);
        $this->renderOptions['products_panel'] = $this->genPanel($pns, [
            'content' => $this->tmplPath('index', '', 'Panel'),
            'toolbars' => [
                $this->genFilterbar('productions_form', 'Products')
            ],
            'table' => $this->genTable('production', 'Products', [
                // 'actions' => true,
                'export' => true,
                'd' => [
                    'ajax' => [
                        'url' => $this->getUrl('ajax', $pns, false, ['pid' => $this->entityId])
                    ],
                    'filters' => $controller::getFilters('productions_table', ['pid' => $this->entityId])
                ]
            ])
        ]);
        $ons = $this->getEntityNameSpaces('Orders');
        $this->addModal([
            'title' => $this->transGenerator->modalTitle('to_production', 'orders'),
            'name' => 'orders_import',
            'en' => $ons['name'],
            'ecn' => $ons['className'],
            'fieldtype' => 'ajson',
            'content' => $this->tmplPath('table', '', 'Modal', self::$bundleName),
            'toolbars' => [
                $this->genFilterbar('productions_form', 'Orders')
            ],
            'buttons' => [
                'add' => [
                    'block' => 'btn_add',
                    'label' => $this->trans($this->transGenerator->btnLabel('addOrders')),
                    'attr' => [
                        'class' => 'btn-success',
                        'id' => 'add_orders_btn'
                    ]
                ]
            ],
            'table' => $this->genTable('to_production', 'Orders', [
                'd' => [
                    'ajax' => [
                        'url' => $this->getUrl('ajax_to_production', $ons, false)
                    ]
                ],
                'select' => [
                    'column' => true,
                    'options' => [
                        'selector' => 'td',
                        'style' => 'multi'
                    ]
                ],
                'actions' => false
            ]),
            'dialog_attr' => [
                'class' => 'modal-xl'
            ]
        ]);
    }

    // </editor-fold>

    //  <editor-fold defaultstate="collapsed" desc="Actions">
    public function createAction1(Request $request, $cid = 0)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->responseMustAjax();
        }
        $dataReturn = [];
        if (!$this->preAction($request, $cid, ['checkPrivilages' => 1])) {
            return $this->responseAccessDenied(true);
        }
        $ids = [];
        $content = $request->getContent();
        if (Utils::is_JSON_string($content)) {
            $data = json_decode($content, true);
            $ids = Utils::extract_array_object_value($data['rows'], 'id');
        }
        $orders = count($ids) > 0 ? $this->getEntityManager()->getRepository(self::getEntityPath('Orders'))->toProduction($ids) : [];
        if (count($orders)) {
            $this->newEntity();
            $dataReturn = $this->entity->addOrders($orders);
            // $dataReturn = $this->entity->genProducts($orders);
            $dataReturn['show'] = true;
            return $this->responseSave('create', $dataReturn);
        } else {
            $dataReturn['errors']['childs'][] = $this->errorMessage(['message' => 'no_orders']);
        }
        return $this->errorsJsonResponse('create', $dataReturn);
    }

    public function generateAction(Request $request, $cid = 0)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->responseMustAjax();
        }
        $dataReturn = [];
        if (!$this->preAction($request, $cid, ['checkPrivilages' => 1])) {
            return $this->responseAccessDenied(true);
        }
        $ids = [];
        $content = $request->getContent();
        if (Utils::is_JSON_string($content)) {
            $data = json_decode($content, true);
            $ids = Utils::extract_array_object_value($data['rows'], 'id');
        }
        $orders = count($ids) > 0 ? $this->getEntityManager()->getRepository(self::getEntityPath('Orders'))->toProduction($ids) : [];
        if (count($orders)) {
            $this->newEntity();
            $dataReturn = $this->entity->addOrders($orders);
            // $dataReturn = $this->entity->genProducts($orders);
            $dataReturn['show'] = true;
            return $this->responseSave('create', $dataReturn);
        } else {
            $dataReturn['errors']['childs'][] = $this->errorMessage(['message' => 'no_orders']);
        }
        return $this->errorsJsonResponse('create', $dataReturn);
    }

    // </editor-fold>
}
