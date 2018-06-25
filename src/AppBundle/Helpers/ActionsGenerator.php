<?php
namespace AppBundle\Helpers;
use AppBundle\Utils\Utils;
// use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
class ActionsGenerator extends ElementsGenerator
{

    // private $ac;// AuthorizationCheckerInterface
    private $th;// TransHelper
    private $rh;// RouterHelper
    // private $twig;
    
    protected $genType='actions';
    protected $type='index';

    private $clientId;
    private $entityId;

    public function __construct( EntityHelper $entityHelper, RouteHelper $routeHelper){
        // $this->ac=$autorizationChecker;
        $this->eh=$entityHelper;
        $this->rh=$routeHelper;
        // $this->twig=$twigEnviroment;
        // $this->sh=$settingsHelper;
        // $this->th=$transHelper;
    }
  
    private function getUrl(string $name, array $parameters=[]):string
    {
        $parameters['id'] = $this->eh->getIdPrototype();
        return $this->clientId ? $this->rh->getClientUrl($name, $this->ecn, $parameters) 
            : $this->rh->getEmployeeUrl($name, $this->ecn, array_replace($parameters, [ 'cid' => $this->clientId]));
    }

    private function setActionUrl(array &$action, string $type, array $actionOptions):void
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
        if($type != 'f'){
            $parameters['type']=$type;
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

    private function setActionTarget(array &$action, string $type, array $actionOptions):void
    {
        $target = Utils::deep_array_value('target', $actionOptions);
        switch ($type) {
            case 'm' :
                $action['d']['toggle'] = 'modal';
                $action['d']['target'] = '#'.(is_null($target) || $target == '1' ? 'my' : $target ).'_modal';
            break;
            case 'p' :
                $action['d']['target'] = '#'.(is_null($target) || $target == '1' ? 'my' : $target ).'_panel';
            break;
            case 'w':
                $action['attr']['target']= ($target) ?: '_blank';
            break;
        }       
    }

    private function generateAction(array $actionOptions):array
    {
        $action=$this->generateElement($actionOptions);
        $action['action']=$actionOptions['action'];
        $action['name']=$actionOptions['action'];
        $type = Utils::deep_array_value('type', $actionOptions, 'f');
        $this->setActionUrl($action, $type, $actionOptions);
        $this->setActionTarget($action, $type, $actionOptions);
        return $action;
    }

    public function generate(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        parent::generate($type, $entityClassName, $options);
        $this->clientId=Utils::deep_array_value('clientId', $options);
        $this->entityId=Utils::deep_array_value('entityId', $options, '__id__' );
        $actions=[];
        foreach($this->getEntityElements() as $name => $action){
            $action=$this->getElement($action);
            $actions[$name]=$this->generateAction($action);
        }
        return $actions;
    }

}