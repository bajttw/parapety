<?php
namespace AppBundle\Helpers;


use Symfony\Component\DependencyInjection\ContainerInterface ;
use AppBundle\Utils\Utils;

class FilterHelper{

    private $predefined=[
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
        ]
    ];

    private $predefinedHidden=[
        'active' => [
            'name' => 'active',
            'value' => '1'
        ]
    ];

    private $sc;
    private $eh;
    private $sh;

    private $options;
    private $filter;

    public function __construct(ContainerInterface $seviceContainer){
        $this->sc=$seviceContainer;
        $this->sh=$seviceContainer->get('helper.settings');
        $this->eh=$seviceContainer->get('helper.entity');
    }

    public function generateHidden($options)
    {
        if(is_string($options)){
            $options=Utils::deep_array_value($options, $this->predefinedHidden);
        }    
        if(!is_array($options)){
            return null;
        }
        $this->options=$options;
        $this->name=$options['name'];
        return [
            'name' => $this->name,
            'type' => 'hidden',
            'value' => $options['value'],
            'options' => Utils::deep_array_value('options', $options, [])
        ];

    }

    public function generate($options):?array
    {
        if(is_string($options)){
            $options=Utils::deep_array_value($options, $this->predefined);
        }    
        if(!is_array($options)){
            return null;
        }
        $this->options=$options;
        $this->name=$options['name'];
        return [
            'name' => $this->name,
            'type' => Utils::deep_array_value('type', $options, 'select'),
            'attr' => Utils::deep_array_value('attr', $options, []),
            'd' => $this->getD(),
            'data' => $this->getData()
        ];
    }

    protected function getD():array{
        $d=Utils::deep_array_value('d', $this->options, []);
        if (array_key_exists('setValue' ,$this->options)) {
            $d['def-value'] = $this->sh->getSettingValue($this->options['setValue']['query']);
        }
        return $d;
    }
    protected function getBanned():array
    {
        $banned=Utils::deep_array_value('banned', $this->options);
        if(is_string($banned)){
            $banned= $this->sh->getSettingValue($banned);
        }
        if(is_array($banned)){
            return $banned;
        }
        return [];
    }

    protected function getData():array
    {
        $data=Utils::deep_array_value('data', $this->options);
        if(is_array($data)){
            return $data;
        }
        $data=[];
        $source=$this->options['source'];
        switch($source['type']){
            case 'settings':
                $banned= $this->getBanned();
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
                $data = $this->eh->getRepository($source['query'])->getFilter(Utils::deep_array_value('options', $source, [])) ;
            break;

        }  
        if(isset($this->options['add'])){
            $start=Utils::deep_array_value('add-start', $this->options);
            $end=Utils::deep_array_value('add-end', $this->options);
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



}