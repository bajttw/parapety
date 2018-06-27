<?php

namespace AppBundle\Entity;
use AppBundle\Utils\Utils;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Uploads;

/**
		* Sizes
		*/
class Sizes extends AppEntity
{
    const en='sizes';
    const ec='Sizes';  
    const idPrototype = '__sid__';
    
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

    public static function getFields($type=null){
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $priceListItems;

    
 // </editor-fold>   
    
 // <editor-fold defaultstate="collapsed" desc="Fields functions"> 
    /**
		* Get id
		*
		* @return integer
		*/
    public function getId(){
        return $this->id;
    }

    /**
		* Set name
		*
		* @param string $name
		*
		* @return Sizes
		*/
    public function setName($name){
        $this->name = $name;
        return $this;
    }

    /**
		* Get name
		*
		* @return string
		*/
    public function getName(){
        return $this->name;
    }

    /**
		* Set symbol
		*
		* @param string $symbol
		*
		* @return Sizes
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
		* @return Sizes
		*/
    public function setDescription($description){
        $this->description = $description;

        return $this;
    }

    /**
		* Get description
		*
		* @return string
		*/
    public function getDescription(){
        return $this->description;
    }

    /**
		* Set sequence
		*
		* @param integer $sequence
		*
		* @return Sizes
		*/
    public function setSequence($sequence){
        $this->sequence = $sequence;

        return $this;
    }

    /**
		* Get sequence
		*
		* @return integer
		*/
    public function getSequence(){
        return $this->sequence;
    }

    /**
		* Set active
		*
		* @param boolean $active
		*
		* @return Sizes
		*/
    public function setActive($active){
        $this->active = $active;

        return $this;
    }

    /**
		* Get active
		*
		* @return boolean
		*/
    public function getActive(){
        return $this->active;
    }

    // /**
    //  * Add priceListItem.
    //  *
    //  * @param \AppBundle\Entity\PriceListItems $priceListItem
    //  *
    //  * @return Sizes
    //  */
    // public function addPriceListItem(\AppBundle\Entity\PriceListItems $priceListItem)
    // {
    //     $this->priceListItems[] = $priceListItem;

    //     return $this;
    // }

    // /**
    //  * Remove priceListItem.
    //  *
    //  * @param \AppBundle\Entity\PriceListItems $priceListItem
    //  *
    //  * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
    //  */
    // public function removePriceListItem(\AppBundle\Entity\PriceListItems $priceListItem)
    // {
    //     return $this->priceListItems->removeElement($priceListItem);
    // }

    // /**
    //  * Get priceListItems.
    //  *
    //  * @return \Doctrine\Common\Collections\Collection
    //  */
    // public function getPriceListItems()
    // {
    //     return $this->priceListItems;
    // }
 // </editor-fold>   
    
    public function getData($jsonEncode=true, $options=[]){
        return parent::getData($jsonEncode, array_replace([ 
                'shortNames' => 'dic'
            ],
            $options
        ));
    }
    
    // /**
    //  * @ORM\PreUpdate
    //  */
    // public function preUpdateSize()
    // {
    //     foreach($this->priceListItems as $priceListItem){
    //         $priceListItem->updateActive();
    //     }
    // }
}
