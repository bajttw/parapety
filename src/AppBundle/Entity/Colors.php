<?php

namespace AppBundle\Entity;
use AppBundle\Utils\Utils;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Uploads;

/**
    * Colors
    */
class Colors extends AppEntity{
    const en='colors';
    const ec='Colors';  
    const idPrototype = '__cid__';
    
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

    // /**
    //  * @var \Doctrine\Common\Collections\Collection
    //  */
    // private $priceListItems;
   
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
		* @return Colors
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
		* @return Colors
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
		* @return Colors
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
		* @return Colors
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
		* @return Colors
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

    /**
		* Set upload
		*
		* @param \AppBundle\Entity\Uploads $upload
		*
		* @return Colors
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

    // /**
    //  * Add priceListItem.
    //  *
    //  * @param \AppBundle\Entity\PriceListItems $priceListItem
    //  *
    //  * @return Colors
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
    // public function preUpdateColor()
    // {
    //     foreach($this->priceListItems as $priceListItem){
    //         $priceListItem->updateActive();
    //         $a=$priceListItem->getActive();
    //     }
    // }
}
