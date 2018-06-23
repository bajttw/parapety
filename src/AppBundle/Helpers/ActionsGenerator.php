<?php
namespace AppBundle\Helpers;
use AppBundle\Utils\Utils;
// use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
class ActionsGenerator extends ElementsGenerator
{

    // private $ac;// AuthorizationCheckerInterface
    private $th;// TransHelper
    // private $twig;
    
    protected $genType='actions';
    protected $type='index';

    public function __construct( EntityHelper $entityHelper){
        // $this->ac=$autorizationChecker;
        $this->eh=$entityHelper;
        // $this->twig=$twigEnviroment;
        // $this->sh=$settingsHelper;
        // $this->th=$transHelper;
    }
  
    private function generateAction(array $actionOptions):array
    {
        $action=$this->generateElement($actionOptions);
        $name=$actionOptions['action'];
        $action['action']=$name;
        $type = Utils::deep_array_value('type', $actionOptions, 'f');
        if ($type != 'f') {
            $target = '#' . (!array_key_exists('target', $actionOptions) || $actionOptions['target'] == '1' ? 'my' : $actionOptions['target']) . '_';
            switch ($type) {
                case 'm' :
                    $action['d']['toggle'] = 'modal';
                    $action['d']['target'] = $target . 'modal';
                break;
                case 'p' :
                    $action['d']['target'] = $target . 'panel';
                break;
            }
        }
        if (Utils::deep_array_value('browserAction', $actionOptions)) {
            $action['d']['action'] = $name;
            $src = Utils::deep_array_value('src', $actionOptions);
            if (isset($src)) {
                $urls[$name] = is_array($src) ? $src['url'] : $this->getUrl($src, $this->ecn, ['id' => $this->eh->getEmptyID() ]);
            }
        }
        else {
            $action['attr']['href'] = $this->getUrl($an, $entityClassName, ['id' => self::emptyEntityID, 'type' => $type]);
            if($type == 'w'){
                $action['attr']['target']=Utils::deep_array_value('target', $action, '_blank');
            }
            $action['d']['url'] = $action['attr']['href'];
        }
        return $action;
    }

    public function generate(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        parent::generate($type, $entityClassName, $options);
        $actions=[];
        foreach($this->getEntityElements() as $name => $action){
            $action=$this->getElement($action);
            $actions[$name]=$this->generateAction($action);
        }
        return $actions;
    }


    // private function genActions($actions):array
    // {
    //     $en =$this->eh->getEntityName($entityClassName);

    //     if (!is_array($actions)) {
    //         $actions = $this->controllerFunction('getActions', $entityClassName, [ $actions ] );
    //     }
    //     if (count($actions) == 0) {
    //         return null;
    //     }
    //     $ac = [];
    //     $urls = [];
    //     foreach ($actions as $action) {
    //         $an = $action['action'];
    //         // $action['attr']['class'] = 'btn-img btn-' . $an . (isset($action['attr']['class']) ? ' ' . $action['attr']['class'] : '');
    //         // if (!isset($action['attr']['title'])) {
    //         //     $action['attr']['title'] = $this->getTransHelper()->btnTitle( $an, $en );
    //         // }
    //         // $type = Utils::deep_array_value('type', $action, 'f');
    //         // if ($type != 'f') {
    //         //     $target = '#' . (!isset($action['target']) || $action['target'] == '1' ? 'my' : $action['target']) . '_';
    //         //     switch ($type) {
    //         //         case 'm' :
    //         //             $action['d']['toggle'] = 'modal';
    //         //             $action['d']['target'] = $target . 'modal';
    //         //             break;
    //         //         case 'p' :
    //         //             $action['d']['target'] = $target . 'panel';
    //         //             break;
    //         //     }
    //         // }
    //         if (Utils::deep_array_value('browserAction', $action)) {
    //             $action['d']['action'] = $an;
    //             $src = Utils::deep_array_value('src', $action);
    //             if (isset($src)) {
    //                 $urls[$an] = is_array($src) ? $src['url'] : $this->getUrl($src, $entityClassName, ['id' => self::emptyEntityID ]);
    //             }
    //         }
    //         else {
    //             $action['attr']['href'] = $this->getUrl($an, $entityClassName, ['id' => self::emptyEntityID, 'type' => $type]);
    //             if($type == 'w'){
    //                 $action['attr']['target']=Utils::deep_array_value('target', $action, '_blank');
    //             }
    //             $action['d']['url'] = $action['attr']['href'];
    //         }
    //         $ac[] = $action;
    //     }
    //     $default = [
    //         'name' => $en,
    //         'actions' => $ac
    //     ];
    //     return [
    //         'urls' => $urls,
    //         'tmpl' => $this->render($this->tmplPath('actions'), array_replace_recursive($default, $options))->getContent()
    //     ];
    // }

    // public function generate( ?string $type = null, ?string $entityClassName = null, array $options = [ 'actions' => 'index' ]):array
    // {
    //     parent::generate($type, $entityClassName, $options);
    //     $table=[];



    //     return $table;
    // }

}