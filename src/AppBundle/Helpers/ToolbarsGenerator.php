<?php
namespace AppBundle\Helpers;
use AppBundle\Utils\Utils;

class ToolbarsGenerator extends ClientElementsGenerator
{

    private $tg;// ToolsGenerator
    
    protected $genType='toolbar';

    private $actions;

    protected function generateElement(?array $toolbarOptions=null):array
    {
        $toolbarOptions= ($toolbarOptions) ?: $this->options;
        $toolbar=parent::generateElement($toolbarOptions);
        $this->setId($toolbar);
        return $toolbar;
    }

    public function __construct( EntityHelper $entityHelper, ToolsGenerator $toolsGenerator){
        $this->eh=$entityHelper;
        $this->tg=$toolsGenerator;
    }
  

    public function generate(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        $this->init($type, $entityClassName, $options);
        $toolbar=$this->generateElement();
        $toolbar['elements']=$this->tg->generate($type, $this->ecn, Utils::deep_array_value('actions', $options, []));
        return $toolbar;
    }

}