<?php

namespace AppBundle\Helpers;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
class DataTablesGenerator extends ElementsGenerator
{
    protected $genType='datatables';
    protected $type='index';

    private $ac;// AuthorizationCheckerInterface
    private $th;// TransHelper
    private $fg;//FilterGenerator

    
    public function __construct(AuthorizationCheckerInterface $autorizationChecker, EntityHelper $entityHelper, SettingsHelper $settingsHelper, TransHelper $transHelper, FiltersGenerator $filtersGenerator){
        $this->ac=$autorizationChecker;
        $this->eh=$entityHelper;
        $this->sh=$settingsHelper;
        $this->th=$transHelper;
        $this->fg=$filtersGenerator;
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

    public function generate( ?string $type = 'index', ?string $entityClassName = null, array $options = [ 'actions' => 'index' ]):array
    {
        parent::generate($type, $entityClassName, $options);
        $table=[];



        return $table;
    }

}