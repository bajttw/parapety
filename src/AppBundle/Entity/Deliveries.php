<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use AppBundle\Utils\Utils;
use Doctrine\Common\Collections\ArrayCollection;
/**
	* Deliveries
	*/
class Deliveries extends AppEntity{
	const en='deliveries';
	const ec='Deliveries';

//  <editor-fold defaultstate="collapsed" desc="Fields utils">    
	public static $shortNames=[
		'id' => 'id',
		'generated' => 'g',
		'number' => 'nr',
		'progress' => 'pr',
		'shipment' => 's',
		'client' => 'cl',
		'childs' => [
			'client' => "Clients",
			'orders' => 'Orders',
			'products' => 'Products',
			'notes' => "Notes"
		]
	];

    public static function getFields($type = null)
    {
        switch ($type) {
            case '':
                $fields = parent::getFields($type);
            break;
            case 'list':
                $fields = [
                    'id',
					'number',
                    [
                        'name' => 'client',
                        'joinField' => [
                            ['name' => 'name'],
                            ['name' => 'code']
                        ]
					],			
                    [
                        'name' => 'generated',
                        'prefix' => 'DATE_FORMAT(',
                        'sufix' => ", '%Y-%m-%d')"
                    ],
                    'progress'
                ];
            break;
            case 'data':
                $fields = ['id', 'number', 'generated', 'progress', 'client'];
            break;
            default:
                $fields = ['id', 'number'];
        }
        return $fields;
    }

    public function getSuccessFields($type)
    {
        $fields = [];
        switch ($type) {
            case 'create':
                $fields = ['generated', 'number'];
            break;
            case 'update':
                $fields = ['progress'];
            break;
            case 'remove':
            default:
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
		* @var \DateTime
		*/
	private $generated;

	/**
		* @var string
		*/
	private $number;

    /**
     * @var int
     */
    private $progress = 0;

	/**
		* @var \DateTime
		*/
	private $shipment;

	/**
		* @var \AppBundle\Entity\Clients
		*/
	private $client;
	
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $orders;

	/**
		* @var \Doctrine\Common\Collections\Collection
		*/
	private $products;	
	
	/**
		* @var \Doctrine\Common\Collections\Collection
		*/
	private $notes;
// </editor-fold>       

//  <editor-fold defaultstate="collapsed" desc="Variables extra">    
  
	private $removedProducts;

// </editor-fold>       

	/**
		* Constructor
		*/
	public function __construct($options=[]){
        $this->orders = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->notes = new ArrayCollection();
        parent::__construct($options);
	}

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
		* Set generated
		*
		* @param \DateTime $generated
		*
		* @return Deliveries
		*/
	public function setGenerated($generated){
		$this->generated = $generated;
		return $this;
	}

	/**
		* Get generated
		*
		* @return \DateTime
		*/
	public function getGenerated(){
		return $this->generated;
	}

	/**
		* Set number
		*
		* @param string $number
		*
		* @return Deliveries
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
     * Set progress
     *
     * @param integer $progress
     *
     * @return Productions
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Get progress
     *
     * @return integer
     */
    public function getProgress()
    {
        return $this->progress;
    }

	/**
		* Set shipment
		*
		* @param \DateTime $shipment
		*
		* @return Deliveries
		*/
	public function setShipment($shipment){
		$this->shipment = $shipment;

		return $this;
	}

	/**
		* Get shipment
		*
		* @return \DateTime
		*/
	public function getShipment(){
		return $this->shipment;
	}

    /**
     * Add order.
     *
     * @param \AppBundle\Entity\Orders $order
     *
     * @return Deliveries
     */
    public function addOrder(\AppBundle\Entity\Orders $order)
    {
        if($order->setDelivery($this)->getDelivery() == $this){
			foreach($order->getProducts() as $p){
				$this->addProduct($p);
			}
            $this->orders[] = $order;
        }
        return $this;
    }

    public function addOrders( $orders =[])
    {
        $oldOrdersCount=$this->orders->count();
        foreach ($orders as $order) {
            $this->addOrder($order);
        }
        return [ 
            'messages' =>[
                'childs' => [
                    $this->controller->responseMessage([ 
                        'message' =>  $this->controller->trans($this->controller->messageText('orders_added'), [ $this->orders->count() - $oldOrdersCount, count($orders)]), 
                        ] , null, false)
                ]
            ]
        ];
    }

    /**
     * Remove order.
     *
     * @param \AppBundle\Entity\Orders $order
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeOrder(\AppBundle\Entity\Orders $order)
    {
		if(is_null( $order->setDelivery()->getDelivery()) ){
            foreach($order->getProducts() as $p){
                $this->removeProduct($p);
            }
            return $this->orders->removeElement($order);
        }
        return null;
    }

    /**
     * Get orders.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    public function getOrdersDataId(){
        $result=[];
        foreach ($this->orders as $order){
            $result[]=$order->getDataId();
        }
        return $result;
    }
	
	/**
		* Add product
		*
		* @param \AppBundle\Entity\Products $product
		*
		* @return Deliveries
		*/
	public function addProduct(\AppBundle\Entity\Products $product){
        $product->setDelivery($this);
		$this->products[] = $product;
		return $this;
	}
	
	/**
		* Remove product
		*
		* @param \AppBundle\Entity\Products $product
		*/
	public function removeProduct(\AppBundle\Entity\Products $product){
		$product->setDelivery();
        $this->products->removeElement($product);
	}
	
	/**
		* Get products
		*
		* @return \Doctrine\Common\Collections\Collection
		*/
	public function getProducts(){
		return $this->products;
	}
	
	/**
		* Set client
		*
		* @param \AppBundle\Entity\Clients $client
		*
		* @return Deliveries
		*/
	public function setClient(\AppBundle\Entity\Clients $client = null){
		$this->client = $client;
		return $this;
	}

	/**
		* Get client
		*
		* @return \AppBundle\Entity\Clients
		*/
	public function getClient(){
		return $this->client;
	}

	/**
		* Add note
		*
		* @param \AppBundle\Entity\Notes $note
		*
		* @return Deliveries
		*/
	public function addNote(\AppBundle\Entity\Notes $note){
		$this->notes[] = $note;

		return $this;
	}

	/**
		* Remove note
		*
		* @param \AppBundle\Entity\Notes $note
		*/
	public function removeNote(\AppBundle\Entity\Notes $note){
		$this->notes->removeElement($note);
	}

	/**
		* Get notes
		*
		* @return \Doctrine\Common\Collections\Collection
		*/
	public function getNotes(){
		return $this->notes;
	}
 // </editor-fold>      



	public function getSuccessData($type){
		$data= parent::getSuccessData($type);
		switch($type){
			case 'created':
			case 'updated':
				$data['orders'] = $this->getOrdersDataId();
			case 'removed':
			default:
		}
		return $data;
	}

	public function getDataDelete(){
		$data=array_replace_recursive(
			parent::getDataDelete(),
			$this->getFieldsStr(['client.code', 'client.name','number', 'generated'])
		);
		return $data;
	}	

	/**
		* @ORM\PrePersist
		*/
	public function prePersistDeliveries(){
        if (is_null($this->generated)) {
            $this->generated = new \DateTime();
        }
	}

    /**
     * @ORM\PreUpdate
     */
    public function preUpdateDeliveries()
    {
        // Add your code here
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemoveDeliveries()
    {
        foreach($this->orders as $order){
            $order->setDelivery();
        }
    }
}
