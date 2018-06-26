<?php
namespace AppBundle\Helpers;


use AppBundle\Utils\Utils;

class ElementsGenerator{

    protected $genType='';

    protected $predefined=[];
  
    protected $eh;//entityHelper
    protected $sh;//settingsHelper

    protected $en;//entityName
    protected $ecn;//entityClassName
    protected $type='';
    protected $options=[];

    protected function getD(array $elementOptions):array
    {
        return Utils::deep_array_value('d', $elementOptions, []);
    }

    protected function getPredefinedElement(string $name, array $predefined)
    {
        return Utils::deep_array_value($name, $predefined);
    }

    protected function choicePredefinedElements(array $elements):array
    {
        $choiced=[];
        if(Utils::array_keys_exists(['predefined','types'], $elements)){
            $types=$elements['types'];
            $type=array_key_exists($this->type, $types) ? $this->type : 'def';           
            if(array_key_exists($type, $types)){
                foreach($types[$type] as $n){
                    $element=$this->getPredefinedElement($n, $elements['predefined']);
                    $choiced[$n]= ($element) ?: $n;
                }
            }
        }
        return $choiced;
    }

    protected function getElement($element):?array
    {
        if(is_string($element)){
            return $this->getPredefinedElement($element, $this->predefined);
        }
        return $element;
    }

    protected function generateElement(array $elementOptions):array
    {
        return [  
            'name' => Utils::deep_array_value('name', $elementOptions, $this->en),         
            'en' => $this->en,
            'ecn' => $this->ecn,
            'attr' => Utils::deep_array_value('attr', $elementOptions, []),
            'd' => $this->getD($elementOptions),
        ];
    }
    protected function getGenericElements():array
    {
        return [];
    }

    protected function getEntityElements():array
    {
        $elements=$this->eh->getElements($this->genType, $this->ecn);
        if(is_null($elements)){
            return $this->getGenericElements();
        }
        return $elements;
    }

    public function generate(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        $this->options=$options;
        $this->ecn=$this->eh->getEntityClassName($entityClassName);
        $this->en=$this->eh->getEntityName($entityClassName);
        if(!is_null($type)){
            $this->type=$type;
        }
        return [];
    }

}