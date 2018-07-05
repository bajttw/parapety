<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use AppBundle\Utils\Utils;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Productions
 */
class Productions extends AppEntity
{
    const en = 'productions';
    const ec = 'Productions';
    const idPrototype = '__pid__';

//  <editor-fold defaultstate="collapsed" desc="Fields utils">    
    public static $shortNames = [
        'id' => 'id',
        'generated' => 'g',
        'number' => 'nr',
        'progress' => 'pr',
        'orders' => 'o',
        'childs' => [
            'orders' => 'Orders',
            'products' => 'Products',
            "notes" => "Notes"
        ]

    ];

    public static function getFields(?string $type = null):array
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
                        'name' => 'generated',
                        'prefix' => 'DATE_FORMAT(',
                        'sufix' => ", '%Y-%m-%d')"
                    ],
                    'progress'
                ];
            break;
            case 'data':
                $fields = ['id', 'number', 'generated', 'progress', 'orders'];
            break;
            default:
                $fields = ['id', 'number'];
        }
        return $fields;
    }

    public function getSuccessFields(?string $type=null):array
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
        return array_replace(parent::getSuccessFields($type), $fields);
    }    

    public function getDeleteFields(?string $type=null):array
    {
        return array_replace(
            parent::getDeleteFields($type), 
            ['number', 'generated']
        );
    }


// </editor-fold>      

//  <editor-fold defaultstate="collapsed" desc="Utilities">

// </editor-fold>

    // public function getShowData($jsonEncode = false, $options = []){
    //     $data=parent::getShowData(false, $options);
    //     $data['orders']=[];
    //     foreach($this->orders as $o){
    //         $data['orders'][]=$o->getShowData(false, [ 'en' => static::en ]);
    //     }
    //     return $jsonEncode ? json_encode($data) : $data;
        
    // }


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
     * @var integer
     */
    private $progress = 0;

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
    public function __construct($options = [])
    {
        parent::__construct($options);
        $this->orders = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public function summary(){
     
    }

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
     * Set generated
     *
     * @param \DateTime $generated
     *
     * @return Productions
     */
    public function setGenerated($generated)
    {
        $this->generated = $generated;
        return $this;
    }

    /**
     * Get generated
     *
     * @return \DateTime
     */
    public function getGenerated()
    {
        return $this->generated;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return Productions
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
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
     * Add order.
     *
     * @param \AppBundle\Entity\Orders $order
     *
     * @return Productions
     */
    public function addOrder(\AppBundle\Entity\Orders $order)
    {
        if($order->setProduction($this)->getProduction() == $this){
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
        if(is_null( $order->setProduction()->getProduction()) ){
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
     * @return Productions
     */
    public function addProduct(\AppBundle\Entity\Products $product)
    {
        $product->setProduction($this);
        $this->products[] = $product;
        return $this;
    }

    /**
     * Remove product
     *
     * @param \AppBundle\Entity\Products $product
     */
    public function removeProduct(\AppBundle\Entity\Products $product)
    {
        if(is_null($this->removedProducts)){
            $this->removedProducts = new ArrayCollection();
        }
        $this->removedProducts->add($product);
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function getRemovedProducts()
    {
        return $this->removedProducts;
    }

    // /**
    //  * Generate products
    //  *
    //  */
    // public function genProducts($orders = [])
    // {
    //     foreach ($orders as $order) {
    //         $products = $order->startProduction();
    //         if(is_array($products)){
    //             foreach ($products as $product) {
    //                 $this->addProduct($product);
    //             }
    //             $this->addOrder($order);
    //         }
    //     }
    //     return [ 
    //         'messages' =>[
    //             'childs' => [
    //                 $this->controller->responseMessage([ 
    //                     'message' =>  $this->controller->trans($this->controller->messageText('orders_added'), [count($orders)]), 
    //                     ] , null, false)
    //             ]
    //         ]
    //     ];
    // }


    
    /**
     * Add note
     *
     * @param \AppBundle\Entity\Notes $note
     *
     * @return Productions
     */
    public function addNote(\AppBundle\Entity\Notes $note)
    {
        $this->notes[] = $note;

        return $this;
    }

    /**
     * Remove note
     *
     * @param \AppBundle\Entity\Notes $note
     */
    public function removeNote(\AppBundle\Entity\Notes $note)
    {
        $this->notes->removeElement($note);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }
 // </editor-fold>       

    public function getSuccessData(?string $type=null):array{
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

 
    /**
     * @ORM\PrePersist
     */
    public function prePersistProductions()
    {
        if (is_null($this->generated)) {
            $this->generated = new \DateTime();
        }
        // $this->summary();
        // $this->calcProgress();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdateProductions()
    {
        // $this->summary();
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemoveProductions()
    {
        foreach($this->orders as $order){
            $order->setProduction();
        }
    }
}
