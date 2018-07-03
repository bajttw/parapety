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

    protected function getName(?array $elementOptions = null, ?string $prefix=null):string
    {
        $elementOptions= ($elementOptions) ?: $this->options;
        $name= Utils::deep_array_value('name', $elementOptions, $this->en) ?: $this->type;
        $prefix = ($prefix) ?: Utils::deep_array_value('namePrefix', $elementOptions);
        return ($prefix) ? $prefix . '_' . $name : $name;
    }

    protected function getId(?string $name=null, ?string $elementType=null):string
    {
        $elementType= ($elementType) ?: $this->genType;
        $name= ($name) ?: $this->en;
        if($name== '1'){
            $name='my';
        }
        return $name . '_' . $elementType;
    }

    protected function getSelector(?string $name=null, ?string $elementType=null):string
    {
        return '#' . $this->getId($name, $elementType);
    }
   
    protected function getD(array $elementOptions):array
    {
        return Utils::deep_array_value('d', $elementOptions, []);
    }

    protected function getAttr(array $elementOptions):array
    {
        return Utils::deep_array_value('attr', $elementOptions, []);
    }

    public function addAttributes(array &$element, array $add, bool $overwrite = false):void
    {
        if (!array_key_exists('attr', $element)) {
            $element['attr'] = $add;
        }else{
            Utils::array_values_set($element['attr'], $add, $overwrite);
        }
    }

    protected function setAttr(string $attrName, $attrValue=null, array &$element, ?array $elementOptions = null, bool $overwrite=false):void
    {
        $elementOptions= $elementOptions ?: $this->options;
        if (!array_key_exists('attr', $element)) {
            $element['attr'] = [];
        }
        if(!array_key_exists($attrName, $element['attr']) || $overwrite ){
            if(is_null($attrValue)){
                $fnGet='get' . ucfirst($attrName);
                if(method_exists($this, $fnGet)){
                    $attrValue=$this->$fnGet($elementOptions);
                }
            }
            $element['attr'][$attrName] = $attrValue; 
        }
    }

    protected function setId(array &$element, ?array $elementOptions = null, ?string $idValue=null  ):void
    {
        $this->setAttr('id', ($idValue) ?: $this->getId($element['name']), $element, $elementOptions);
    }

    protected function addClass(array &$element, $class):void
    {
        Utils::addClass($element, $class, 'attr');
    }

    protected function getLabel(string $name)
    {
        return $this->th->trans($this->th->labelText($this->genType . '.' . $name, $this->en));
    }

    protected function getTitle(string $name)
    {
        return $this->th->trans($this->th->titleText($this->genType . '.' . $name, $this->en));
    }


    protected function setTitle(array &$element, ?array $elementOptions = null, ?string $title= null):void
    {
        $this->setAttr('title', ($title) ?: $this->getTitle($element['name']), $element, $elementOptions);
    }

    protected function setLabel(array &$element):void
    {
        $element['label']=$this->getLabel($element['name']);
    }

    protected function setFieldLabel(array &$element):void
    {
        $element['fileldLabel']['label']=$this->getLabel($element['name']);
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

    protected function getChildOptions(string $childType, ?array $elementOptions=null ):?array
    {
        $childOpt=Utils::deep_array_value(['childs', $childType], ($elementOptions) ?: $this->options);
        if($childOpt && !is_array($childOpt)){
            $childOpt=[
                'type' => is_string($childOpt) ? $childOpt : $this->type
            ];
        }     
        if(is_array($childOpt)){
            Utils::deep_array_value_set('type', $childOpt, $this->type);
        }
        return $childOpt;
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

    protected function setElementProperty(string $propertyName, array &$element, ?array $elementOptions = null, bool $set=false):void
    {
        $elementOptions= $elementOptions ?: $this->options;
        $data=Utils::deep_array_value($propertyName, $elementOptions);
        if(is_null($data)){
            $fnGet='get' . ucfirst($propertyName);
            if(method_exists($this, $fnGet)){
                $data=$this->$fnGet($elementOptions);
            }
        }
        if($set || $data){
            $element[$propertyName]=$data;
        }
    }

    protected function setValue(array &$element, ?array $elementOptions=null, bool $set=false):void
    {
        $this->setElementProperty('value', $element, $elementOptions, $set);
    }

    protected function setData(array &$element, ?array $elementOptions = null, bool $set=false):void
    {
        $this->setElementProperty('data', $element, $elementOptions, $set);
    }

    protected function setSettings(array &$element, ?array $elementOptions = null, bool $set=false):void
    {
        $this->setElementProperty('settings', $element, $elementOptions, $set);
    }

    protected function setContent(array &$element, ?array $elementOptions=null, bool $set =true):void
    {
        $this->setElementProperty('content', $element, $elementOptions, $set);
    }

    protected function getValue(array $elementOptions)
    {
        return null;
    }

    protected function getData(array $elementOptions):?array
    {
        return null;
    }

    protected function getSettings(array $elementOptions):?array
    {
        return null;
    }

    protected function getContent(array $elementOptions):?string
    {
        return null;
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
            $type=$this->type;
            if(!array_key_exists($type, $types)){
                $s= explode('_', $type);
                $type= count($s) > 1 ? $s[0] . '_' . 'def' : 'def';
            }
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

    protected function generateElement(?array $elementOptions=null):array
    {
        $elementOptions= ($elementOptions) ?: $this->options;
        return [  
            'name' => $this->getName($elementOptions),         
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