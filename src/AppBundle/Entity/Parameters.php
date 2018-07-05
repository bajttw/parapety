<?php

namespace AppBundle\Entity;
use AppBundle\Entity\Uploads;

/**
		* Parameters
		*/
class Parameters extends AppEntity
{
    const en='parameters';
    const ec='Parameters';  
    const idPrototype = '__pid__';

//  <editor-fold defaultstate="collapsed" desc="Fields utils">    
    public static $dicNames=[
        'id' => 'v',
        'name' => 'n',
        'symbol' => 's',
        'description' => 'd'
    ];

    public static $shortNames=[
        'id' => 'id',
        'sequence' => 'sq',
        'name' => 'n',
        'symbol' => 's',
        'description' => 'd',
        'active' => 'a'
    ];

    public static function getFields(?string $type=null):array{
        $fields=[];
        switch ($type){
            case '':
                $fields=parent::getFields($type);
            break;
            case 'list':
                $fields=['id', 'sequence', 'name', 'symbol', 'description', 'active'];
            break;
            case 'filter':
                $fields=['id', 'name'];
            break;
            case 'data':
            case 'dic':
            default:
                $fields=['id', 'name', 'symbol', 'description'];
        }
        return $fields;
    }

    public function getSuccessFields(?string $type=null):array
    {
        $fields = [];
        switch ($type) {
            case 'create':
                $fields = ['sequence'];
            break;
            case 'update':
                $fields = [];
            break;
            case 'remove':
                $fields = ['name', 'symbol'];
            default:
        }
        return array_replace(parent::getSuccessFields($type), $fields);
    }      

    public function getMessageDataFields(?string $type=null):array
    {
        $fields = [];
        switch ($type) {
            default:
                $fields = ['name', 'symbol'];
        }
        return array_replace( parent::getMessageDataFields($type), $fields );
    }

    public function getDeleteFields(?string $type=null):array
    {
        return array_replace(
            parent::getDeleteFields($type), 
            [ 'symbol', 'name' ]
        );
    }


// </editor-fold>       
        
//  <editor-fold defaultstate="collapsed" desc="Variables">   
    /**
        * @var integer
        */
    protected $id;

    /**
        * @var string
        */
    protected $name;

    /**
        * @var string
        */
    protected $symbol;

    /**
        * @var string
        */
    protected $description;

    /**
        * @var integer
        */
    protected $sequence;

    /**
        * @var boolean
        */
    protected $active = true;

// </editor-fold>   

// <editor-fold defaultstate="collapsed" desc="Fields functions"> 
    /**
        * Get id
        *
        * @return integer
        */
    public function getId():?int
    {
        return $this->id;
    }

    /**
        * Set name
        *
        * @param string $name
        *
        * @return Parameters
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
    public function getName():?string
    {
        return $this->name;
    }

    /**
        * Set symbol
        *
        * @param string $symbol
        *
        * @return Parameters
        */
    public function setSymbol(string $symbol){
        $this->symbol = $symbol;
        return $this;
    }

    /**
        * Get symbol
        *
        * @return string
        */
    public function getSymbol():?string
    {
        return $this->symbol;
    }

    /**
        * Set description
        *
        * @param string $description
        *
        * @return Parameters
        */
    public function setDescription(?string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
        * Get description
        *
        * @return string
        */
    public function getDescription():?string
    {
        return $this->description;
    }

    /**
        * Set sequence
        *
        * @param integer $sequence
        *
        * @return Parameters
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
    public function getSequence():?int{
        return $this->sequence;
    }

    /**
        * Set active
        *
        * @param boolean $active
        *
        * @return Parameters
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

    // </editor-fold>   

    public function getData(array $options=[]):array
    {
        return parent::getData(array_replace([ 
                'shortNames' => 'dic'
            ],
            $options
        ));
    }

}
