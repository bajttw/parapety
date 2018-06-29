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

    private function getElementOptions(string $elementType):?array
    {
        $elementOpt=Utils::deep_array_value(['elements', $elementType], $this->options);
        if($elementOpt && !is_array($elementOpt)){
            $elementOpt=[
                'type' => is_string($elementOpt) ? $elementOpt : $this->type
            ];
        }     
        if(is_array($elementOpt)){
            Utils::deep_array_value_set('type', $elementOpt, $this->type);
        }
        return $elementOpt;
    }

    protected function setToolbar(array &$panel):void
    {
        if(is_array(Utils::deep_array_value('toolbars-toolbar', $panel))){
            return;
        }
        $toolbar=$this->getElementOptions('toolbar');
        if(is_array($toolbar)){
            $oToolbar= \array_replace_recursive(
                ($this->clientId) ? [ 'clientId' => $this->clientId ] : [],
                Utils::deep_array_value('options', $toolbar, [])
            );
            $panel['toolbars']['toolbar']= $this->tbg->generate(
                $toolbar['type'],
                $this->ecn,
                $oToolbar
            );
        }
    }

    protected function setFilterbar(array &$panel):void
    {
        if(is_array(Utils::deep_array_value('toolbars-filterbar', $panel))){
            return;
        }
        $filterbar=$this->getElementOptions('filterbar');
        if(is_array($filterbar)){
            $panel['toolbars']['filterbar']= $this->fbg->generate(
                $filterbar['type'],
                $this->ecn,
                Utils::deep_array_value('options', $filterbar, [])
            );
        }
    }

    protected function setTable(array &$panel):void
    {
        $panel['table']=Utils::deep_array_value('table', $this->options);
        if(is_array($panel['table'])){
            return;
        }
        $table=$this->getElementOptions('table');
        if(is_array($table)){
            $oTable= \array_replace_recursive(
                ($this->clientId) ? [ 'clientId' => $this->clientId ] : [],
                Utils::deep_array_value('options', $table, [])
            );
            $panel['table'] = $this->dtg->generate(
                $table['type'],
                $this->ecn,
                $oTable
            );
        }
    }

    protected function setContent(array &$panel):void
    {
        $panel['content']=Utils::deep_array_value('content', $this->options );
        if(is_null($panel['content'])){
            $panel['content'] = $this->tmplh->getPath(
                Utils::deep_array_value('contentType', $this->options, $this->type),
                true,
                $this->ecn,
                'p'
            );
        }
    }

    public function generate(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        $this->init($type, $entityClassName, $options);
        $panel=$this->generateElement($options);
        $panel['toolbars']=Utils::deep_array_value('toolbars', $this->options, []);
        $this->setId($panel);
        $this->setContent($panel);    
        $this->setTable($panel);
        $this->setToolbar($panel);
        $this->setFilterbar($panel);
        if(Utils::deep_array_value('active', $this->options)){
            $panel['active']=true;
        }
        return $panel;
    }

    // public function generatePanels(?string $type=null, array $entityClassNames, array $options=[]):array
    // {
    //     $panels=[];
    //     foreach ($entityClassNames as $ec) {
    //         $this->init($type, $entityClassName, $options);

    //         $this->generate($type, $ec, $options)

    //     }
    // }
}