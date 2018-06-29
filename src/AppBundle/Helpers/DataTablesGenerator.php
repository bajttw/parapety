<?php

namespace AppBundle\Helpers;
use AppBundle\Utils\Utils;

class DataTablesGenerator extends ClientElementsGenerator
{
    private $ac;// AuthorizationCheckerInterface
    private $fg;//FilterGenerator
    private $eag;//EntityActionsGenerator

    protected $genType='table';
    
    public function __construct( EntityHelper $entityHelper, SettingsHelper $settingsHelper, TransHelper $transHelper, FiltersGenerator $filtersGenerator, EntityActionsGenerator $entityActionsGenerator, RouteHelper $routeHelper){
        $this->eh=$entityHelper;
        $this->rh=$routeHelper;
        $this->sh=$settingsHelper;
        $this->th=$transHelper;
        $this->fg=$filtersGenerator;
        $this->eag=$entityActionsGenerator;
    }

    // private function getUrl(string $type, array $parameters):string
    // {
    //     return $this->clientId ? $this->rh->getClientUrl($type, $this->ecn, array_replace($parameters, [ 'cid' => $this->clientId])) 
    //             : $this->rh->getEmployeeUrl($type, $this->ecn, $parameters);
    // }

    private function setAjax(array &$table):void
    {
        if(Utils::deep_array_value('d-ajax', $table)){
            return;
        }
        $urlType=Utils::deep_array_value('ajax-urlType', $this->options, 'ajax');
        $parameters=Utils::deep_array_value('ajax-parameters', $this->options, []);
        $table['d']['ajax']=[
            'dataSrc' => '',
            'url' => $this->getUrl($urlType, $parameters)
        ];
    }

    private function setEntityUrls(array &$table):void
    {
        $table['d']['entity-urls']=[];
        $actions=Utils::deep_array_value('entityUrls', $this->options, []);
        foreach($actions as $action){
            $parameters=[];
            if(is_array($action)){
                $parameters=Utils::deep_array_value('parameters', $action, []);
                $name=$action['name'];
            }else{
                $name=$action;
            }
            $parameters['id'] = $this->eh->getIdPrototype();
            $table['d']['entity-urls'][$action]=$this->getUrl($urlType, $parameters);
        }
    }

    private function setFilters(array &$table){
        if(!Utils::deep_array_key_exists('d-filters', $table)){
            $filters=Utils::deep_array_value('ajax-filters', $this->options, []);
            if(is_array($filters)){
                $table['d']['filters']=$this->fg->generateTableFilters(
                    Utils::deep_array_value('type', $filters, $this->type ), 
                    $this->ecn, 
                    \array_replace_recursive([ 
                            'values' => [
                                'client' => (int)$this->clientId
                            ]
                        ],
                        Utils::deep_array_value('options', $filters, [])
                    )
                );
            }
        }
    }

    protected function getD(array $elementOptions):array
    {
        return array_replace_recursive([
                'columns' => [ 
                        [ 'data' => 'id' ]
                    ]
            ],
            $this->eh->getSettingsValue('tables-options', $this->ecn) ?: [],
            Utils::deep_array_value('d', $elementOptions, []),
            $this->sh->getTableSettings( $this->en, $this->type) ?: [] 
        );
    }

    private function setActions(array &$table):void
    {
        $oActions= Utils::deep_array_value('actions', $this->options, $this->type);
        if(!$oActions){
            return ;
        }
        if(is_array($oActions)){
            $tActions=Utils::deep_array_value('type', $oActions, $this->type);
            $cActions=Utils::deep_array_value('column', $oActions);
        }else{
            $tActions=$oActions;
        }
        $actions=$this->eag->generate($tActions, $this->ecn);
        foreach($actions as $a){
            if(array_key_exists('url', $a )){
                $table['d']['entity-urls'][$a['name']]=$a['url'];
            }
        }
        $table['d']['columns'][]=\array_replace_recursive([
                'tmpl' => [
                    'actions' => $actions,
                    'block' => 'datatable_actions'
                ],
                'label' => 'actions',
                'data' => null,
                'className' => 'dt-actions',
                'searchable' => false,
                'orderable' => false,
                'render' => 'actions'
            ],
            isset($cActions) ? $cActions : []
        );
    }

    private function setSelect(array &$table):void
    {
        $select= Utils::deep_array_value('select', $this->options );
        if(!$select){
            return ;
        }
        if (!is_array($select)) {
            $select = is_string($select) ? ['options' => ['style' => $select]] : [];
        }
        $oSelect=Utils::deep_array_value('options', $select, []);
        Utils::array_values_set($oSelect, [
            'style' => 'single',
            'selector' => 'tr'
        ]);
        if (array_key_exists('column', $select)) {
            $cSelect = array_replace_recursive(
                [
                    'data' => 'sel',
                    'className' => 'dt-select',
                    'searchable' => false,
                    'orderable' => false,
                    'defaultContent' => "",
                    'render' => "sel"
                ],
                is_array($select['column']) ? $select['column'] : []
            );
            if ($oSelect['selector'] == 'td') {
                $oSelect['selector'] = 'td.' . $cSelect['className'];
            }
            \array_unshift($table['d']['columns'], $cSelect);
        }
        $table['d']['select'] = $oSelect;
    }

    private function setDetails(array &$table):void
    {
        $details = Utils::deep_array_value('details', $this->options);
        if(!$details){
            return;
        }
        if (!is_array($details)) {
            $details = is_string($details) ? ['options' => ['render' => $details]] : [];
        }
        $oDetails = Utils::deep_array_value('options', $details, []);
        Utils::deep_array_value_set('render', $oDetails, $this->en);
        $column = Utils::deep_array_value('column', $details, true);
        if ($column) {
            array_unshift($columns, array_replace_recursive([
                    'label' => 'det',
                    'data' => null,
                    'className' => 'dt-detail',
                    'searchable' => false,
                    'orderable' => false,
                    'defaultContent' => ""
                ],
                is_array($column) ? $column : []
            ));
        }
        $default['d']['details'] = $oDetails;
        $default['d']['ajax']['url'] = $this->getUrl('ajax_details');     
    }

    public function generate( ?string $type = 'index', ?string $entityClassName = null, array $options = [ ]):array
    {
        $this->init($type, $entityClassName, $options);
        $table=$this->generateElement($options);
        $this->setId($table);
        $this->setAjax($table);
        $this->setFilters($table);
        $this->setEntityUrls($table);
        $this->setActions($table);
        $this->setSelect($table);
        $this->setDetails($table);
        return $table;
    }

}