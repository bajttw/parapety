<?php
namespace AppBundle\Helpers;
use AppBundle\Utils\Utils;

class ActionsGenerator extends ClientElementsGenerator
{

    protected $renderType="m";

    public function __construct( EntityHelper $entityHelper, RouteHelper $routeHelper, TransHelper $transHelper){
        $this->eh=$entityHelper;
        $this->rh=$routeHelper;
        $this->th=$transHelper;
    }
  
    protected function setActionUrl(array &$action, array $actionOptions):void
    {
        $urlOptions=Utils::deep_array_value('url', $actionOptions );
        $name=$action['name'];
        $parameters=[];
        if(is_array($urlOptions)){
            if(isset($urlOptions['action'])){
                $name=$urlOptions['action'];   
            }
            if(is_array($urlOptions['parameters'])){
                $parameters= $urlOptions['parameters'];
            }
        }else if(is_string($urlOptions)){
            $name=$urlOptions;
        }
        if($actionOptions['renderType'] != 'f'){
            $parameters['type']= $actionOptions['renderType'];
        }
        if (Utils::deep_array_value('browserAction', $actionOptions)) {
            $action['d']['action'] = $name;
            $action['attr']['href']='#';
            $src = Utils::deep_array_value('src', $actionOptions);
            if (isset($src)) {
                $action['url'] = is_array($src) ? $src['url'] : $this->getUrl($src, $parameters);
            }      
        }else{
            $url= $this->getUrl($name, $parameters);
            $action['attr']['href'] = $url;
            $action['d']['url'] = $url;
        }
    }

    protected function setActionTarget(array &$action, array $actionOptions):void
    {
        $target = Utils::deep_array_value('target', $actionOptions);
        switch ($actionOptions['renderType']) {
            case 'm' :
                $action['d']['toggle'] = 'modal';
                $action['d']['target'] = $this->getSelector($target, 'modal');
            break;
            case 'p' :
                $action['d']['target'] = $this->getSelector($target, 'panel');;
            break;
            case 'w':
                $action['attr']['target']= ($target) ?: '_blank';
            break;
        }       
    }

    protected function generateAction(array $actionOptions):array
    {
        Utils::deep_array_value_set('renderType', $actionOptions, $this->renderType);
        $action=$this->generateElement($actionOptions);
        $action['action']=$actionOptions['action'];
        $action['name']=$actionOptions['action'];
        $this->setActionUrl($action, $actionOptions);
        $this->setActionTarget($action, $actionOptions);
        $this->setLabel($action);
        $this->setTitle($action);
        return $action;
    }


    public function generate(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        $this->init($type, $entityClassName, $options);
        $actions=[];
        foreach($this->choicePredefinedElements($this->getEntityElements()) as $name => $action){
            $actions[$name]=$this->generateAction($this->getElement($action));
        }
        return $actions;
    }
}