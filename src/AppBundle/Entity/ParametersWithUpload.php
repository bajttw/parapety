<?php

namespace AppBundle\Entity;

/**
		* ParametersWithUpload
		*/
class ParametersWithUpload extends Parameters
{
    const en='parameterswithupload';
    const ec='ParametersWithUpload';  
    const idPrototype = '__pid__';
    
//  <editor-fold defaultstate="collapsed" desc="Fields utils">    
    public static $dicNames=[
        'id' => 'v',
        'name' => 'n',
        'symbol' => 's',
        'description' => 'd',
        'upload' => 'u',
        'childs' => [
            'upload' => 'Uploads'
        ]
    ];

    public static $shortNames=[
        'id' => 'id',
        'sequence' => 'sq',
        'name' => 'n',
        'symbol' => 's',
        'description' => 'd',
        'active' => 'a',
        'upload' => 'u',
        'childs' => [
            'upload' => 'Uploads'
        ]
    ];

    public static function getFields(?string $type=null):array{
        switch ($type){
            case '':
                $fields=parent::getFields($type);
            break;
            case 'list':
                $fields=['id', 'sequence', 'name', 'symbol', 'description', 'active', 
                    [
                        'name' => 'upload',
                        'joinField' => [
                            [ 'name' => 'name'],
                            [ 'name' => 'url']
                        ]
                    ]
                ];
            break;
            case 'filter':
                $fields=['id', 'name'];
            break;
            case 'data':
                $fields=['id', 'name', 'symbol', 'description'];
            break;
            case 'dic':
            default:
                $fields=['id', 'name', 'symbol', 'description', [
                    'name' => 'upload',
                    'joinField' => [
                        [ 'name' => 'name'],
                        [ 'name' => 'url']
                    ]
                ]
            ];
        }
        return $fields;
    }

    public function getSuccessFields(?string $type=null):array
    {
        $fields = [];
        switch ($type) {
            case 'create':
                $fields = ['sequence', 'upload'];
            break;
            case 'update':
                $fields = ['upload'];
            break;
            case 'remove':
                $fields = ['name', 'symbol'];
            default:
        }
        return array_replace(parent::getSuccessFields($type), $fields);
    }      

// </editor-fold>       
    
//  <editor-fold defaultstate="collapsed" desc="Variables">   

    /**
		* @var \AppBundle\Entity\Uploads
		*/
    protected $upload;

// </editor-fold>       

// <editor-fold defaultstate="collapsed" desc="Fields functions"> 

    /**
		* Set upload
		*
		* @param \AppBundle\Entity\Uploads $upload
		*
		* @return ParametersWithUpload
		*/
    public function setUpload(\AppBundle\Entity\Uploads $upload = null){
        $this->upload = $upload;

        return $this;
    }

    /**
		* Get upload
		*
		* @return \AppBundle\Entity\Uploads
		*/
    public function getUpload(){
        return $this->upload;
    }

 // </editor-fold> 
    
}
