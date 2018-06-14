<?php
namespace AppBundle\Entity;

/**
 * Products
 */
class Products extends AppEntity
{
    const en = 'products';
    const ec = 'Products';

 //  <editor-fold defaultstate="collapsed" desc="Fields utils">    
    public static $shortNames = [
        'id' => 'id',
		'number' => 'nr', 
        'label' => 'l',
        'clientLabel' => 'cl',
        'barcode' => 'p',
        'info' => 'i',
        'comment' => 'c',
        'cutTime' => 'ct',
        'packingTime' => 'pt',
        'childs' => [
            'position' => 'Positions',
            'production' => 'Productions',
            'delivery' => 'Deliveries',
            'packager' => 'Users'
        ]

    ];

    public static function getFields($type = null)
    {
        switch ($type) {
            default:
                $fields = parent::getFields($type);
        }
        return $fields;
    }
 // </editor-fold>       

 //  <editor-fold defaultstate="collapsed" desc="Utilities">    
 // </editor-fold>       

 //  <editor-fold defaultstate="collapsed" desc="Variables">    
    /**
     * @var integer
     */
    private $id;

	/**
		* @var string
     */
    private $number;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $clientLabel;

    /**
     * @var string
     */
    private $barcode;

    /**
     * @var string
     */
    private $info;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var \DateTime
     */
    private $cutTime;

    /**
     * @var \DateTime
     */
    private $packingTime;

    /**
     * @var \AppBundle\Entity\Positions
     */
    private $position;

    /**
     * @var \AppBundle\Entity\Productions
     */
    private $production;

    /**
     * @var \AppBundle\Entity\Deliveries
     */
    private $delivery;

    /**
     * @var \AppBundle\Entity\Users
     */
    private $packager;
 // </editor-fold>       

 //  <editor-fold defaultstate="collapsed" desc="Variables extra">    
 // </editor-fold>       


 // <editor-fold defaultstate="collapsed" desc="Fields functions"> 
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

	/**
		* Set number
		*
		* @param string $number
		*
		* @return Products
		*/
    public function setNumber($number){
        $this->number = $number;

        return $this;
    }
    
    /**
        * Get number
        *
        * @return string
        */
    public function getNumber(){
        return $this->number;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return Products
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set clientLabel
     *
     * @param string $clientLabel
     *
     * @return Products
     */
    public function setClientLabel($clientLabel)
    {
        $this->clientLabel = $clientLabel;

        return $this;
    }

    /**
     * Get clientLabel
     *
     * @return string
     */
    public function getClientLabel()
    {
        return $this->clientLabel;
    }

    /**
     * Set barcode
     *
     * @param string $barcode
     *
     * @return Products
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * Get barcode
     *
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * Set info
     *
     * @param string $info
     *
     * @return Products
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Products
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set cutTime
     *
     * @param \DateTime $cutTime
     *
     * @return Products
     */
    public function setCutTime($cutTime)
    {
        $this->cutTime = $cutTime;

        return $this;
    }

    /**
     * Get cutTime
     *
     * @return \DateTime
     */
    public function getCutTime()
    {
        return $this->cutTime;
    }

    /**
     * Set packingTime
     *
     * @param \DateTime $packingTime
     *
     * @return Products
     */
    public function setPackingTime($packingTime)
    {
        $this->packingTime = $packingTime;

        return $this;
    }

    /**
     * Get packingTime
     *
     * @return \DateTime
     */
    public function getPackingTime()
    {
        return $this->packingTime;
    }

    /**
     * Set position
     *
     * @param \AppBundle\Entity\Positions $position
     *
     * @return Products
     */
    public function setPosition(\AppBundle\Entity\Positions $position = null)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return \AppBundle\Entity\Positions
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set production
     *
     * @param \AppBundle\Entity\Productions $production
     *
     * @return Products
     */
    public function setProduction(\AppBundle\Entity\Productions $production = null)
    {
        $this->production = $production;

        return $this;
    }

    /**
     * Get production
     *
     * @return \AppBundle\Entity\Productions
     */
    public function getProduction()
    {
        return $this->production;
    }

    /**
     * Set delivery
     *
     * @param \AppBundle\Entity\Deliveries $delivery
     *
     * @return Products
     */
    public function setDelivery(\AppBundle\Entity\Deliveries $delivery = null)
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * Get delivery
     *
     * @return \AppBundle\Entity\Deliveries
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * Set packager
     *
     * @param \AppBundle\Entity\Users $packager
     *
     * @return Products
     */
    public function setPackager(\AppBundle\Entity\Users $packager = null)
    {
        $this->packager = $packager;

        return $this;
    }

    /**
     * Get packager
     *
     * @return \AppBundle\Entity\Users
     */
    public function getPackager()
    {
        return $this->packager;
    }
 // </editor-fold>       

    /**
     * @ORM\PrePersist
     */
    public function prePersistProducts()
    {
        if(is_null($this->barcode)){
            $this->setBarcode($this->id);
        }
    }
}
