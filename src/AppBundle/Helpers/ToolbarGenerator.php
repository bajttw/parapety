<?php
namespace AppBundle\Helpers;
use AppBundle\Utils\Utils;

class ToolbarGenerator extends ClientElementsGenerator
{

    private $tg;// ToolsGenerator
    
    protected $genType='toolbar';

    private $actions;

    public function __construct( EntityHelper $entityHelper, ToolsGenerator $toolsGenerator){
        $this->eh=$entityHelper;
        $this->tg=$toolsGenerator;
    }
  
    public function generate(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        $this->init($type, $entityClassName, $options);
        $toolbar=$this->generateElement($options);
        $this->setId($toolbar);
        $toolbar['elements']=$this->tg->generate($type, $this->ecn, Utils::deep_array_value('actions', $options, []));
        return $toolbar;
    }

}