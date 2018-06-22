<?php

namespace AppBundle\Helpers;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
class DataTableHelper
{

    private $ac;// AuthorizationCheckerInterface
    private $eh;// EntityHelper
    private $sh;// SettingsHelper
    private $th;// TransHelper

    private $options;
    private $ecn;
    private $en;
    private $type;

    public function __construct(AuthorizationCheckerInterface $autorizationChecker, EntityHelper $entityHelper, SettingsHelper $settingsHelper, TransHelper $transHelper){
        $this->ac=$autorizationChecker;
        $this->eh=$entityHelper;
        $this->sh=$settingsHelper;
        $this->th=$transHelper;
    }

    private function getGenericActions(string $type='index'):array
    {
        return [];
    }

    private function getEntityActions(string $type='index'):array
    {
        $getFunction='get'. $this->ecn. 'Actions';       
        return method_exists($this, $getFunction) ? $this->$getFunction($type) : $this->getGenericActions($type);
    }

    
    private function genActions($actions):array
    {
        $en =$this->eh->getEntityName($entityClassName);

        if (!is_array($actions)) {
            $actions = $this->controllerFunction('getActions', $entityClassName, [ $actions ] );
        }
        if (count($actions) == 0) {
            return null;
        }
        $ac = [];
        $urls = [];
        foreach ($actions as $action) {
            $an = $action['action'];
            $action['attr']['class'] = 'btn-img btn-' . $an . (isset($action['attr']['class']) ? ' ' . $action['attr']['class'] : '');
            if (!isset($action['attr']['title'])) {
                $action['attr']['title'] = $this->getTransHelper()->btnTitle( $an, $en );
            }
            $type = Utils::deep_array_value('type', $action, 'f');
            if ($type != 'f') {
                $target = '#' . (!isset($action['target']) || $action['target'] == '1' ? 'my' : $action['target']) . '_';
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
            if (Utils::deep_array_value('browserAction', $action)) {
                $action['d']['action'] = $an;
                $src = Utils::deep_array_value('src', $action);
                if (isset($src)) {
                    $urls[$an] = is_array($src) ? $src['url'] : $this->getUrl($src, $entityClassName, ['id' => self::emptyEntityID ]);
                }
            }
            else {
                $action['attr']['href'] = $this->getUrl($an, $entityClassName, ['id' => self::emptyEntityID, 'type' => $type]);
                if($type == 'w'){
                    $action['attr']['target']=Utils::deep_array_value('target', $action, '_blank');
                }
                $action['d']['url'] = $action['attr']['href'];
            }
            $ac[] = $action;
        }
        $default = [
            'name' => $en,
            'actions' => $ac
        ];
        return [
            'urls' => $urls,
            'tmpl' => $this->render($this->tmplPath('actions'), array_replace_recursive($default, $options))->getContent()
        ];
    }

    protected function genTable( string $tableType = 'index', ?string $entityClassName = null, array $options = [ 'actions' => 'index' ])
    {
        $this->type = $tableType;
        $this->options=$options;
        $this->ecn=$this->eh->getEntityClassName($entityClassName);
        $this->en=$this->eh->getEntityName($entityClassName);

    }
}