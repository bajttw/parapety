<?php
namespace AppBundle\Helpers;
use AppBundle\Utils\Utils;

class ModalsGenerator extends ElementsGenerator
{

    
    protected $genType='modal';


    private $actions;

    public function __construct( EntityHelper $entityHelper, TransHelper $transHelper){
        $this->eh=$entityHelper;
        $this->th=$transHelper;
    }
  
    public function generate(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        $this->init($type, $entityClassName, $options);
        $modal=$this->generateElement($options);
        $this->setId($modal);
        return $modal;
    }

}