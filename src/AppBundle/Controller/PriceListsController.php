<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Controller\AppController;
use AppBundle\Entity\Prices;
use AppBundle\Utils\Utils;

/**
 * PriceLists controller.
 *
 */
class PriceListsController extends AppController
{
    const en = 'pricelists';
    const ec = 'PriceLists';

    public static function getFilters($type = 'index', $options = [])
    {
        $id = Utils::deep_array_value('id', $options);
        $cid = Utils::deep_array_value('cid', $options);
        $isClient = $cid != null;
        $filters = [];
        $fs = [
            'start' => [
                'name' => 'start',
                'type' => 'input',
                'label' => 'pricelists.label.start',
                'setValue' => [
                    'type' => 'settings',
                    'query' => 'pricelists-filters-start-value'
                ],
                'source' => [
                    'type' => 'settings',
                    'query' => 'pricelists-filters-dateRanges'
                ],
                'd' => [
                    'filter-options' => json_encode(['type' => 'date_period']),
                    'widget' => 'daterange'
                ]
            ],
            'end' => [
                'name' => 'end',
                'type' => 'input',
                'label' => 'pricelists.label.end',
                'setValue' => [
                    'type' => 'settings',
                    'query' => 'pricelists-filters-end-value'
                ],
                'source' => [
                    'type' => 'settings',
                    'query' => 'pricelists-filters-dateRanges'
                ],
                'd' => [
                    'filter-options' => json_encode(['type' => 'date_period']),
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
            'client_hidden' => [
                'name' => 'clients.id',
                'type' => 'hidden',
                'value' => $isClient ? [ $cid ] : [],
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
            ]
                
        ];
        switch ($type) {
            case 'index':
                foreach (['start', 'end', 'clientsGroups','clients'] as $f) {
                    self::addFilter($filters, $fs[$f], $f);
                }
            break;
            case 'service':
                foreach (['start', 'end'] as $f) {
                    self::addFilter($filters, $fs[$f], $f);
                }
            break;
            case 'table_client':
                self::addFilter($filters, self::genFilter('client_hidden', $options), 'client');
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
            'delete' => ['action' => 'delete', 'type' => 'm', 'target' => 'pricelists']
        ];
        switch ($type) {
            case 'index':
                $as = ['show', 'xls',  'edit', 'delete'];
            break;
            case 'view':
            default:
                $as = ['show'];
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

    protected function customCreateAction(&$dataReturn){
        $dataReturn['toEdit'] = true;
        return $dataReturn;
    } 

    protected function customEditAction(Request $request, $id, $cid = 0)
    {
        $this->renderOptions['entity_data'] = $this->entity->getShowData();
    }

    protected function customNewEntity(){
        if ($this->isClient()) {
            $this->entity->addClient($this->client);        
        }
        
    }

    public static function genCustomSettings($controller, &$entitySettings = [])
    {
        foreach (['PriceListItems'] as $ecn) {
            $entitySettings['dictionaries'][$ecn] = $controller->getDic($ecn);
        }
        return $entitySettings;
    }

    protected function setCustomFormOptions()
    {
        $this->formOptions['attr']['data-admin'] = $this->isAdmin();
        $this->formOptions['attr']['data-form'] = self::en;
        $this->formOptions['clients'] = $this->entity->getClients()->count() > 0;
        // $this->formOptions['attr']['data-price-list-items'] =  $this->getDic('PriceListItems');
        $plins = $this->getEntityNameSpaces('PriceListItems');
        $this->addModal([
            'title' => $this->genTranslateText('to_pricelist', 'modal.title', $plins),
            'name' => 'pricelistitems_import',
            'en' => $plins['name'],
            'ecn' => $plins['className'],
            'fieldtype' => 'ajson',
            'content' => $this->tmplPath('table', '', 'Modal', self::$bundleName),
            'toolbars' => [
                $this->genFilterbar($plins, 'pricelists_form')
            ],
            'buttons' => [
                'add' => [
                    'block' => 'btn_add',
                    'label' => $this->trans($this->labelText('btn.addPriceListItems')),
                    'attr' => [
                        'class' => 'btn-success',
                        'id' => 'add_prices_btn'
                    ]
                ]
            ],
            'table' => $this->genTable($plins, 'to_pricelist', [
                'd' => [
                    'ajax' => [
                        'url' => $this->getUrl('ajax_to_pricelist', $plins, false)
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
        
        return $this;
    }

  

    // </editor-fold>

    //  <editor-fold defaultstate="collapsed" desc="Actions">

    // </editor-fold>
}
