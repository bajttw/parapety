<?php

namespace AppBundle\Helpers;
// use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use AppBundle\Utils\Utils;


class DataTablesGenerator extends ElementsGenerator
{
    private $ac;// AuthorizationCheckerInterface
    private $th;// TransHelper
    private $rh;// RouteHelper
    private $fg;//FilterGenerator
    private $ag;//ActionsGenerator

    protected $genType='datatables';
    protected $type='index';

    private $clientId;

    
    public function __construct( EntityHelper $entityHelper, SettingsHelper $settingsHelper, TransHelper $transHelper, FiltersGenerator $filtersGenerator, ActionsGenerator $actionsGenerator, RouteHelper $routeHelper){
        // $this->ac=$autorizationChecker;
        $this->eh=$entityHelper;
        $this->rh=$routeHelper;
        $this->sh=$settingsHelper;
        $this->th=$transHelper;
        $this->fg=$filtersGenerator;
        $this->ag=$actionsGenerator;
    }
  

    private function getUrl(string $type, array $parameters):string
    {
        return $this->clientId ? $this->rh->getClientUrl($type, $this->ecn, array_replace($parameters, [ 'cid' => $this->clientId])) 
                : $this->rh->getEmployeeUrl($type, $this->ecn, $parameters);
    }

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
        $filtersType=Utils::deep_array_value('ajax-filtersType', $this->options);

    }

    protected function getD(array $elementOptions):array
    {
        return array_replace_recursive([
                [
                    'columns' => [ 
                     'data' => 'id' 
                    ]
                ]
            ],
            $this->eh->getSettingsValue('tables-options', $this->ecn) ?: [],
            Utils::deep_array_value('d', $elementOptions, [])
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
        $actions=$this->ag->generate($tActions);
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
        $oSelect= Utils::deep_array_value('select', $this->options );
        if(!$oSelect){
            return ;
        }
        if(is_array($oSelect)){
            $tActions=Utils::deep_array_value('type', $oActions, $this->type);
            $cActions=Utils::deep_array_value('column', $oActions);
        }else{
            $tActions=$oActions;
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

    public function generate( ?string $type = 'index', ?string $entityClassName = null, array $options = [ ]):array
    {
        parent::generate($type, $entityClassName, $options);
        $this->clientId=Utils::deep_array_value('clientId', $options);
        $table=$this->generateElement($options);
        $table['name']=Utils::deep_array_value('name', $options, $this->en);
        $table['d']=array_replace_recursive(
            $table['d'],
            $this->sh->getTableSettings( $this->en, $type) ?: [] 
        );
        $this->setAjax($table);
        $this->setEntityUrls($table);
        $this->setActions($table);
        $columns=&$table['d']['columns'];

        return $table;
    }

}