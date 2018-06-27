<?php
namespace AppBundle\Helpers;


use AppBundle\Utils\Utils;

class ElementsGenerator{

    protected $eh;//entityHelper
    protected $sh;//settingsHelper
    protected $th;//transHelper
  
    protected $genType='';
    protected $type='index';

    protected $predefined=[];
  
    protected $en;//entityName
    protected $ecn;//entityClassName
    protected $options=[];

    protected function init(?string $type=null,  ?string $entityClassName=null, array $options=[]):void
    {
        $this->options=$options;
        $this->ecn=$this->eh->getEntityClassName($entityClassName);
        $this->en=$this->eh->getEntityName($entityClassName);
        if(!is_null($type)){
            $this->type=$type;
        }
    }

    protected function getId(?string $type=null, ?string $elementType=null):string
    {
        $elementType= ($elementType) ?: $this->genType;
        $type= ($type) ?: $this->en;
        if($type== '1'){
            $type='my';
        }
        return $type . '_' . $elementType;
    }

    protected function getSelector(?string $type=null, ?string $elementType=null):string
    {
        return '#' . $this->getId($type, $elementType);
    }
   
    protected function getD(array $elementOptions):array
    {
        return Utils::deep_array_value('d', $elementOptions, []);
    }

    protected function getAttr(array $elementOptions):array
    {
        return Utils::deep_array_value('attr', $elementOptions, []);
    }

    protected function setAttr(array &$element, string $key, $value):void
    {
        $element['attr'][$key]=$value;
    }

    protected function genLabel(string $name)
    {
        return $this->th->trans($this->th->labelText($this->genType . '.' . $name, $this->en));
    }

    protected function setLabel(array &$element):void
    {
        $element['label']=$this->genLabel($element['name']);
    }

    protected function setFieldLabel(array &$element):void
    {
        $element['fileldLabel']['label']=$this->genLabel($element['name']);
    }

    protected function setFieldAttr(array &$element, array $elementOptions):void
    {
        $this->setChildFormAttr($element);
        if($element['type'] != 'hidden'){
            $element['fieldLabel'] = Utils::deep_array_value('fieldLabel', $elementOptions, []);
            if(!array_key_exists('label', $element['fieldLabel'])){
                $this->setFieldLabel($element);
            }
            $this->setTitle($element);
        }         
    }
    
    protected function setTitle(array &$element):void
    {
        $element['attr']['title']=$this->th->titleText($this->genType . '.' . $element['name'], $this->en);
    }

    protected function setChildId(array &$element, ?string $parentId =null ):void
    {
        $parentId= ($parentId) ?: Utils::deep_array_value('parentId', $this->options);
        if($parentId){
            $element['attr']['id']=$parentId . '_' . $element['name'];
        }
    }

    protected function setChildFormAttr(array &$element, ?string $parentId =null ):void
    {
        $parentId= ($parentId) ?: Utils::deep_array_value('parentId', $this->options);
        if($parentId){
            $element['attr']['id']=$parentId . '_' . $element['name'];
            $element['attr']['name']=$parentId . '[' . $element['name'] . ']';
        }
    }

    protected function setId(array &$element, ?string $idValue = null ):void
    {
        $element['attr']['id']= ($idValue) ?: $this->getId();
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
            'attr' => $this->getAttr($elementOptions),
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


}