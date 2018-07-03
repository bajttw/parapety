<?php

namespace AppBundle\Entity;
use AppBundle\Utils\Utils;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Uploads;

/**
		* Trims
		*/
class Trims extends AppEntity
{
    const en='trims';
    const ec='Trims';  
    const idPrototype = '__tid__';
    
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

    public static function getFields($type=null){
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
 // </editor-fold>       

 //  <editor-fold defaultstate="collapsed" desc="Variables">   
    /**
		* @var integer
		*/
    private $id;

    /**
		* @var string
		*/
    private $name;

    /**
		* @var string
		*/
    private $symbol;

    /**
		* @var string
		*/
    private $description;

    /**
		* @var integer
		*/
    private $sequence;

    /**
		* @var boolean
		*/
    private $active = true;

    /**
		* @var \AppBundle\Entity\Uploads
		*/
    private $upload;
 // </editor-fold>       

 // <editor-fold defaultstate="collapsed" desc="Fields functions"> 
    /**
		* Get id
		*
		* @return integer
		*/
    public function getId():int{
        return $this->id;
    }

    /**
		* Set name
		*
		* @param string $name
		*
		* @return Trims
		*/
    public function setName(string $name){
        $this->name = $name;
        return $this;
    }

    /**
		* Get name
		*
		* @return string
		*/
    public function getName():?string{
        return $this->name;
    }

    /**
		* Set symbol
		*
		* @param string $symbol
		*
		* @return Trims
		*/
    public function setSymbol($symbol){
        $this->symbol = $symbol;

        return $this;
    }

    /**
		* Get symbol
		*
		* @return string
		*/
    public function getSymbol(){
        return $this->symbol;
    }

    /**
		* Set description
		*
		* @param string $description
		*
		* @return Trims
		*/
    public function setDescription(?string $description){
        $this->description = $description;

        return $this;
    }

    /**
		* Get description
		*
		* @return string
		*/
    public function getDescription():?string{
        return $this->description;
    }

    /**
		* Set sequence
		*
		* @param integer $sequence
		*
		* @return Trims
		*/
    public function setSequence(int $sequence){
        $this->sequence = $sequence;

        return $this;
    }

    public function initSequence(int $sequence){
        return $this->setSequence($sequence);
    }

    /**
		* Get sequence
		*
		* @return integer
		*/
    public function getSequence():int{
        return $this->sequence;
    }

    /**
		* Set active
		*
		* @param boolean $active
		*
		* @return Trims
		*/
    public function setActive(bool $active){
        $this->active = $active;

        return $this;
    }

    /**
		* Get active
		*
		* @return boolean
		*/
    public function getActive():bool{
        return $this->active;
    }

    /**
		* Set upload
		*
		* @param \AppBundle\Entity\Uploads $upload
		*
		* @return Trims
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

    public function getData(bool $jsonEncode=true, array $options=[])
    {
        return parent::getData($jsonEncode, array_replace([ 
                'shortNames' => 'dic'
            ],
            $options
        ));
    }

    
}
