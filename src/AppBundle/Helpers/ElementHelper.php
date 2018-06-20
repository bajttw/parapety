<?php
namespace AppBundle\Helpers;


use Symfony\Component\DependencyInjection\ContainerInterface ;
use AppBundle\Utils\Utils;

class ElementHelper{

    protected $sc;
    protected $eh;

    public function __construct(ContainerInterface $seviceContainer){
        $this->sc=$seviceContainer;
        $this->eh=$seviceContainer->get('helper.entity');
    }
 
 
    public function getElementId(string $elementName, ?string $entityClassName=null):string
    {

    }
}