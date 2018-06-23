<?php
namespace AppBundle\Helpers;


// use Symfony\Component\DependencyInjection\ContainerInterface ;
use AppBundle\Utils\Utils;

class FiltersGenerator extends ElementsGenerator{

    protected $genType='filters';
    protected $type='index';
    
    protected $predefined=[
        'active' => [
            'name' => 'active',
            'data' => [
                ['v' => '1', 'n' => 'aktywny'],
                ['v' => '0', 'n' => 'nieaktywny']
            ],
            'd' => [
                'widget' => 'multiselect'
            ],
            'attr' => [
                'multiple' => 'multiple'
            ]
        ],
        'client' => [
            'name' => 'client',
            'source' => [
                'type' => 'entity',
                'query' => 'Clients',
            ],
            'attr' => [
                'multiple' => 'multiple'
            ],
            'd' => [
                'widget' => 'multiselect'                
            ]
        ],
        'hidden' => [
            'active' => [
                'name' => 'active',
                'value' => '1'
            ],
            'client' => [
                'name' => 'client',
                'source' => [
                    'type' => 'options',
                    'query' => 'client'
                ]
            ]   
        ]
    ];


    public function __construct(EntityHelper $entityHelper, SettingsHelper $settingsHelper){
        $this->eh=$entityHelper;
        $this->sh=$settingsHelper;
    }

    protected function getPredefinedElement(string $name, array $defined)
    {
        $filter=parent::getPredefinedElement($name, $defined);
        if(is_array($filter) && strpos($name, 'hidden-') === 0 ){
            $filter['type']='hidden';
        }
        return $filter;
    }

    private function getValue(array $filterOptions)
    {
        if(array_key_exists('value', $filterOptions)){
            return $filterOptions['value'];
        }
        $value=null;
        $source=$filterOptions['source'];
        switch($source['type']){
            case 'settings':
                $value=$this->sh->getSettingValue($source['query']);
            break ;
            case 'options':
                $value=Utils::deep_array_value(['values' , $source['query']], $this->options);
            break;
        }
        switch (Utils::deep_array_value('valueType', $source)){
            case 'set_or_null':
                $value= is_null($value) ? null : [null, $value];
            break;
        }
        return $value;
    }

    private function getData(array $filterOptions):array
    {
        $data=Utils::deep_array_value('data', $filterOptions);
        if(is_array($data)){
            return $data;
        }
        $data=[];
        $source=$filterOptions['source'];
        switch($source['type']){
            case 'settings':
                $banned= $this->getBannedValues($filterOptions);
                $rows=$this->sh->getSettingValue($source['query']);
                if(is_array($rows)){
                    foreach($rows as $row){
                        if(is_array($banned) && count($banned) && in_array(array_key_exists('v', $row) ? $row['v'] : $row['value'] , $banned)){
                            continue;
                        }
                        $data[]=$row;
                    }
                }
            break;
            case 'entity':
                // $repository=$this->getEntityManager()->getRepository(Utils::deep_array_value('repository', $source, self::$bundlePath . Utils::deep_array_value('query', $source, '')));
                $data = $this->eh->getRepository($source['query'])->getElement(Utils::deep_array_value('options', $source, [])) ;
            break;
        }  
        if(isset($filterOptions['add'])){
            $start=Utils::deep_array_value('add-start', $filterOptions);
            $end=Utils::deep_array_value('add-end', $filterOptions);
            if(is_array($start)){
                foreach($start as $row){
                    array_unshift($data, $row);
                }
            }
            if(is_array($end)){
                foreach($end as $row){
                    array_push($data, $row);
                }
            }
        }
        return $data;

    }

    private function getBannedValues(array $filterOptions):array
    {
        $banned=Utils::deep_array_value('banned', $filterOptions);
        if(is_string($banned)){
            $banned= $this->sh->getSettingValue($banned);
        }
        return is_array($banned) ? $banned : [];
    }

    protected function getD(array $elementOptions):array
    {
        $d=Utils::deep_array_value('d', $elementOptions, []);
        if (array_key_exists('setValue' ,$elementOptions)) {
            $d['def-value'] = $this->sh->getSettingValue($elementOptions['setValue']['query']);
        }
        return $d;
    }

    private function generateHiddenFilter(array $filterOptions):array
    {
        return [
            'name' => $filterOptions['name'],
            'type' => 'hidden',
            'value' => $this->getValue($filterOptions),
            'options' => Utils::deep_array_value('options', $filterOptions, [])
        ];
    }

    private function generateFilter(array $filterOptions):array
    {
        $filter=$this->generateElement($filterOptions);
        $filter['name'] = $filterOptions['name'];
        $filter['type'] = Utils::deep_array_value('type', $filterOptions, 'select');
        $filter['data'] = $this->getData($filterOptions);
        return $filter;
    }

    public function generate(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        parent::generate($type, $entityClassName, $options);
        $filters=[
            'visible' => [],
            'hidden' => []
        ];
        foreach($this->getEntityElements() as $name => $filter){
            $filter=$this->getElement($filter);
            $fname= strpos($name, 'hidden-') === false ? $name : substr($name, 7) ;
            if (Utils::deep_array_value('type', $filter) == 'hidden'){
                $filters['hidden'][$fname] = $this->generateHiddenFilter($filter);
            }else{
                $filters['visible'][$fname] = $this->generateFilter($filter);
            }
        }
        return $filters;
    }

    protected function getGenericElements():array
    {
        $elements=[
            'predefined' => [
                'active' => 'active'
            ],
            'types' => [
                'def' => ['active']
            ]
        ];
        return $this->choicePredefinedElements($elements); 
    }



}