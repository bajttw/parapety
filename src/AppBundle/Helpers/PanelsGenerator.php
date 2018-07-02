<?php
namespace AppBundle\Helpers;
use AppBundle\Utils\Utils;

class PanelsGenerator extends ClientElementsGenerator
{
    
    protected $genType='panel';
    protected $type='index';

    protected $tbg;
    protected $fbg;
    protected $dtg;
    protected $tmplh;

    public function __construct( EntityHelper $entityHelper, ToolbarsGenerator $toolbarsGenerator, FilterbarsGenerator $filterbarsGenerator, DataTablesGenerator $dtGenerator, TemplateHelper $templateHelper){
        $this->eh=$entityHelper;
        $this->tbg=$toolbarsGenerator;
        $this->fbg=$filterbarsGenerator;
        $this->dtg=$dtGenerator;
        $this->tmplh=$templateHelper;
    }
  
    // protected function getId(?string $type=null, ?string $elementType=null):string
    // {
    //     $elementType= ($elementType) ?: $this->genType;
    //     return $this->getName($type) . '_' . $elementType;
    // }

    protected function setToolbars(array &$panel, ?array $panelOptions=null, bool $set =true):void
    {
        $panelOptions = ($panelOptions) ?: $this->options;
        $panel['toolbars']=Utils::deep_array_value('toolbars', $panelOptions, []);
        if(!is_array(Utils::deep_array_value('toolbars-toolbar', $panel))){
            $toolbar=$this->getToolbar($panelOptions);
            if(is_array($toolbar)){
                $panel['toolbars']['toolbar']=$toolbar;
            }
        }
        if(!is_array(Utils::deep_array_value('toolbars-filterbar', $panel))){
            $filterbar=$this->getToolbar($panelOptions);
            if(is_array($filterbar)){
                $panel['toolbars']['filterbar']=$filterbar;
            }
        }
    }

    protected function getToolbar(array $panelOptions):?array
    {
        $toolbar=$this->getChildOptions('toolbar', $panelOptions);
        if(is_array($toolbar)){
            $oToolbar= \array_replace_recursive(
                ($this->clientId) ? [ 'clientId' => $this->clientId ] : [],
                Utils::deep_array_value('options', $toolbar, [])
            );
            return $this->tbg->generate(
                $toolbar['type'],
                $this->ecn,
                $oToolbar
            );
        }
        return null;
    }

    protected function getFilterbar(array $panelOptions):?array
    {
        $filterbar=$this->getChildOptions('filterbar', $panelOptions);
        if(is_array($filterbar)){
            return $this->fbg->generate(
                $filterbar['type'],
                $this->ecn,
                Utils::deep_array_value('options', $filterbar, [])
            );
        }
        return null;
    }

    protected function getTable(array $panelOptions):?array
    {
        $table=$this->getChildOptions('table', $panelOptions);
        if(is_array($table)){
            $oTable= \array_replace_recursive(
                ($this->clientId) ? [ 'clientId' => $this->clientId ] : [],
                Utils::deep_array_value('options', $table, [])
            );
            return $this->dtg->generate(
                $table['type'],
                $this->ecn,
                $oTable
            );
        }
        return null;
    }

    protected function setTable(array &$panel, ?array $panelOptions=null, bool $set =true):void
    {
        $this->setElementProperty('table', $panel, $panelOptions, $set);
    }

    protected function getContent(array $panelOptions):?string
    {
        $contentName = Utils::deep_array_value('contentName', $panelOptions);
        if($contentName){
            return $this->tmplh->getPath(
                $contentName,
                $this->ecn,
                Utils::deep_array_value('contentGeneric', $panelOptions, true),
                'p'
            );
        }
        return null;
    }

    public function generate(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        $this->init($type, $entityClassName, $options);
        $panel=$this->generateElement();
        $this->setId($panel);
        $this->setContent($panel);    
        $this->setTable($panel);
        $this->setToolbars($panel);
        if(Utils::deep_array_value('active', $this->options)){
            $panel['active']=true;
        }
        return $panel;
    }

}