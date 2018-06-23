<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Utils\Utils;

/**
 * Orders
 */
class Orders extends AppEntity
{
    const en = 'orders';
    const ec = 'Orders';
    const emptyId = '__oid__';

    const numerate = false;
 //  <editor-fold defaultstate="collapsed" desc="Fields utils">

    public static $shortNames = [
        'id' => 'id',
        'created' => 'cr',
        'approved' => 'ap',
        'term' => 't',
        'intNr' => 'inr',
        'number' => 'nr',
        'clientNumber' => 'cnr',
        'quantity' => 'q',
        'area' => 'a',
        'value' => 'v',
        'status' => 's',
        'progress' => 'pr',
        'color' => 'c',
        'model' => 'm',
        'size' => 's',
        'trims' => 'ts',
        'express' => 'exp',
        'clientComment' => 'cc',
        'orderComment' => 'oc',
        'prodComment' => 'pc',
        'clientOrderId' => 'co',
        'clientInfo' => 'ci',
        'client' => 'cl',
        'childs' => [
            'positions' => 'Positions',
            'client' => 'Clients',
            'model' => 'Models',
            'size' => 'Sizes',
            'color' => 'Colors',
            'trims' => 'Trims',
            'production' => 'Productions',
            'delivery' => 'Deliveries',
            'uploads' => 'Uploads',
            'notes' => 'Notes'
        ]
    ];

    public static function getFields($type = null)
    {
        switch ($type) {
            case '':
                $fields = parent::getFields($type);
            break;
            case 'productions':
                $fields = [
                    'id',
                    'number',
                    'clientNumber',
                    [
                        'name' => 'client',
                        'joinField' => [
                            ['name' => 'name'],
                            ['name' => 'code']
                        ]
                    ],
                    [
                        'name' => 'created',
                        'prefix' => 'DATE_FORMAT(',
                        'sufix' => ", '%Y-%m-%d')"
                    ],
                    [
                        'name' => 'approved',
                        'prefix' => 'DATE_FORMAT(',
                        'sufix' => ", '%Y-%m-%d')"
                    ],
                    'quantity',
                    'orderComment',
                    'clientComment',
                    'positions'
                ];
            break;
            case 'packages':
                $fields = [
                    'id',
                    'number',
                    'clientNumber',
                    [
                        'name' => 'created',
                        'prefix' => 'DATE_FORMAT(',
                        'sufix' => ", '%Y-%m-%d')"
                    ],
                    'approved',
                    'orderComment',
                    'clientComment'
                ];
            break;
            case 'list':
                $fields = [
                    'id',
                    [
                        'name' => 'client',
                        'joinField' => [
                            ['name' => 'name'],
                            ['name' => 'code']
                        ]
                    ],
                    'number',
                    'clientNumber',
                    [
                        'name' => 'created',
                        'prefix' => 'DATE_FORMAT(',
                        'sufix' => ", '%Y-%m-%d')"
                    ],
                    [
                        'name' => 'approved',
                        'prefix' => 'DATE_FORMAT(',
                        'sufix' => ", '%Y-%m-%d')"
                    ],
                    'quantity',
                    'area',
                    'express',
                    [
                        'name' => 'term',
                        'prefix' => 'DATE_FORMAT(',
                        'sufix' => ", '%Y-%m-%d')"
                    ],
                    'status',
                    [
                        'name' => 'positions.size',
                        'joinField' => [
                             'name' => 'id',
                             'prefix' => 'GROUP_CONCAT(DISTINCT ',
                             'sufix' => ')',
                             'as' => 'sizes'
                        ]
                    ],
                    'progress'
                ];
            break;
            case 'productions_data':
            case 'deliveries_data':
            case 'list_to_production':
            case 'list_to_delivery':
                $fields = [
                    'id',
                    [
                        'name' => 'client',
                        'joinField' => [
                            ['name' => 'name'],
                            ['name' => 'code']
                        ]
                    ],
                    'number',
                    'clientNumber',
                    [
                        'name' => 'created',
                        'prefix' => 'DATE_FORMAT(',
                        'sufix' => ", '%Y-%m-%d')"
                    ],
                    [
                        'name' => 'approved',
                        'prefix' => 'DATE_FORMAT(',
                        'sufix' => ", '%Y-%m-%d')"
                    ],
                    'quantity',
                    'area',
                    'express',
                    [
                        'name' => 'term',
                        'prefix' => 'DATE_FORMAT(',
                        'sufix' => ", '%Y-%m-%d')"
                    ],
                    'status',
                    [
                        'name' => 'positions.size',
                        'joinField' => [
                             'name' => 'id',
                             'prefix' => 'GROUP_CONCAT(DISTINCT ',
                             'sufix' => ')',
                             'as' => 'sizes'
                        ]
                    ],
                    'orderComment',
                    'prodComment'
                ];
            break;
            case 'data':
                $fields = ['id', 'number', 'created', 'approved', 'area'];
            break;
            // case 'productions_data':
            // 	$fields=['id', 'client', 'number', 'created', 'approved', 'area'];
            // break;
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
                $fields = ['created', 'approved', 'number', 'clientNumber'];
            break;
            case 'update':
                $fields = ['approved', 'status'];
            break;
            case 'remove':
            default:
        }
        return $fields;
    }

    public function getMessageDataFields($type)
    {
        $fields = [];
        switch ($type) {
            case 'create':
            case 'update':
                $fields = ['number', 'created'];
            break;
            case 'remove':
            default:
        }
        return $fields;
    }


// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Utilities">
    public function mmCalcArea()
    {
        $mmArea = 0;
        foreach ($this->positions as $position) {
            $mmArea += $position->mmCalcArea();
        }
        return $mmArea;
    }

    public function calcArea()
    {
        $mmArea = $this->mmCalcArea();
        $this->area = $mmArea / 1000000;
    }

    public function summary()
    {
        $this->calcArea();
        $this->quantity = 0;
        foreach ($this->positions as $position) {
            $this->quantity += $position->getQuantity();
        }
    }

// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Variables">

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $approved;

    /**
     * @var \DateTime
     */
    private $term;

    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $clientNumber;

    /**
     * @var integer
     */
    private $quantity = 1;

    /**
     * @var float
     */
    private $area = 0;

    /**
     * @var float
     */
    private $value = 0;

    /**
     * @var integer
     */
    private $status = 1;

    /**
     * @var integer
     */
    private $progress = 0;

    /**
     * @var string
     */
    private $trims;

    /**
     * @var integer
     */
    private $express = 1;

    /**
     * @var string
     */
    private $clientComment;

    /**
     * @var string
     */
    private $orderComment;

    /**
     * @var string
     */
    private $prodComment;

    /**
     * @var integer
     */
    private $clientOrderId;

    /**
     * @var string
     */
    private $clientInfo;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $positions;

    /**
     * @var \AppBundle\Entity\Clients
     */
    private $client;

    /**
     * @var \AppBundle\Entity\Models
     */
    private $model;

    /**
     * @var \AppBundle\Entity\Sizes
     */
    private $size;

    /**
     * @var \AppBundle\Entity\Colors
     */
    private $color;

    /**
     * @var \AppBundle\Entity\Productions
     */
    private $production;

    /**
     * @var \AppBundle\Entity\Deliveries
     */
    private $delivery;

    /**
     * @var \AppBundle\Entity\Invoices
     */
    private $invoice;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $uploads;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $notes;

// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Variables extra">
    private $oldPositions;

// </editor-fold>

    /**
     * Constructor
     */
    public function __construct($options = [])
    {
        $this->positions = new ArrayCollection();
        $this->uploads = new ArrayCollection();
        $this->notes = new ArrayCollection();
        parent::__construct($options);
    }

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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Orders
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    public function getShowCreated($options = [])
    {
        return parent::getTimeField($this->created, $options);
    }

    public function getStrCreated()
    {
        return $this->getShowCreated(['strDate' => 'time']);
    }

    /**
     * Set approved
     *
     * @param \DateTime $approved
     *
     * @return Orders
     */
    public function setApproved($approved = null)
    {
        if (!$this->approved) {
            $this->approved = $approved;
        } elseif (!$approved) {
            $this->approved = null;
        }
        return $this;
    }

    /**
     * Get approved
     *
     * @return \DateTime
     */
    public function getApproved()
    {
        return $this->approved;
    }

    public function getShowApproved($options = [])
    {
        return parent::getTimeField($this->approved, $options);
    }

    public function getStrApproved()
    {
        return $this->getShowApproved(['strDate' => 'time']);
    }

    public function approve()
    {
        switch ($this->status) {
            case '1':
                $this->setApproved(null);
            break;
            case '2':
                $this->setApproved(new \DateTime());
            break;
        }
    }



    /**
     * Set term
     *
     * @param \DateTime $term
     *
     * @return Orders
     */
    public function setTerm($term)
    {
        $this->term = $term;
        return $this;
    }

    /**
     * Get term
     *
     * @return \DateTime
     */
    public function getTerm()
    {
        return $this->term;
    }

    public function getShowTerm($options = [])
    {
        return parent::getTimeField($this->term, $options);
    }

    public function getStrTerm()
    {
        return $this->getShowTerm(['strDate' => 'time']);
    }

    public function genTerm($days = 0)
    {
        $terms = [['v' => 1, 'n' => 21], ['v' => 2, 'n' => 14], ['v' => 3, 'n' => 21]];
        $add = 0;
        if (is_array($days)) {
            $terms = $days;
        } elseif ($days > 0) {
            $add = $days;
        }
        $def = Utils::from_dictionary($terms, 1, ['name' => 'n', 'empty' => 21]);
        if ($add == 0 && $this->getExpress() > 1) {
            $add = Utils::from_dictionary($terms, $this->getExpress(), ['name' => 'n', 'empty' => $def]);
        } else {
            $add = $this->client && $this->client->getTerm() ? $this->client->getTerm() : $def;
        }
        $this->term = (new \DateTime())->add(new \DateInterval('P' . $add . 'D'));
        // $this->term->add(new DateInterval('P'.$days.'D'));
        return $this->term;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return Orders
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
     * Set clientNumber
     *
     * @param string $clientNumber
     *
     * @return Orders
     */
    public function setClientNumber($clientNumber)
    {
        $this->clientNumber = $clientNumber;

        return $this;
    }

    /**
     * Get clientNumber
     *
     * @return string
     */
    public function getClientNumber()
    {
        return $this->clientNumber;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Orders
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set area
     *
     * @param float $area
     *
     * @return Orders
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return float
     */
    public function getArea()
    {
        return $this->area;
    }

    public function getShowArea()
    {
        return round($this->area, 3);
    }

    /**
     * Set value
     *
     * @param float $value
     *
     * @return Orders
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Orders
     */
    public function setStatus($status = null)
    {
        $setStatus = false;
        switch ($status) {
            case '1'://nowe
                $setStatus = is_null($this->status) || $this->status < 3;
            break;
            case '2'://złożone
                $setStatus = $this->status < 4 && $this->positions->count() > 0;
            break;
            case '3'://obsługa
                $setStatus = $this->status == 2 || ($this->status == 4 && $this->progress == 0);
            break;
            case '4'://produkcja
                $setStatus = $this->status > 1;
            break;
            case '5'://wysłane
                $setStatus = $this->status == 4 && $this->progress == 100;
            break;
            default:
                if ($this->positions->count()) {
                    if (is_null($this->status) || $this->status < 2) {
                        $this->status = is_null($this->getApproved()) ? 1 : 2;
                    }
                } else {
                    $this->status = 1;
                }
        }
        if ($setStatus) {
            $this->status = $status;
        }
        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set progress
     *
     * @param integer $progress
     *
     * @return Orders
     */
    public function setProgress($progress)
    {
        $this->progress = intval($progress);
        // if($this->progress!== $progress){
        //     $oldProgress=$this->progress;
        //     $this->progress = $progress;
        //     if($oldProgress == null || $oldProgress == 0 ){
        //         $this->setStatus(4);
        //     }elseif($progress==0){
        //         $this->setStatus(3);
        //     }
        // }
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

    public function calcProgress()
    {
        $progress = 0;
        foreach ($this->getPositions() as $position) {
            $progress += $position->getProgress();
        }
        $this->setProgress($progress / $this->getPositions()->count());
        return $this->progress;
    }

    /**
     * Set trims
     *
     * @param string $trims
     *
     * @return Orders
     */
    public function setTrims($trims)
    {
        $this->trims = $trims;

        return $this;
    }

    // public function initTrims($controller, $trims)
    // {
    //     $this->trims = $trims;

    //     return $this;
    // }

    /**
     * Get trims
     *
     * @return string
     */
    public function getTrims()
    {
        return $this->trims;
    }

    /**
     * Set express
     *
     * @param integer $express
     *
     * @return Orders
     */
    public function setExpress($express)
    {
        $this->express = $express;

        return $this;
    }

    /**
     * Get express
     *
     * @return integer
     */
    public function getExpress()
    {
        return $this->express;
    }

    /**
     * Set clientComment
     *
     * @param string $clientComment
     *
     * @return Orders
     */
    public function setClientComment($clientComment)
    {
        $this->clientComment = $clientComment;

        return $this;
    }

    /**
     * Get clientComment
     *
     * @return string
     */
    public function getClientComment()
    {
        return $this->clientComment;
    }

    /**
     * Set orderComment
     *
     * @param string $orderComment
     *
     * @return Orders
     */
    public function setOrderComment($orderComment)
    {
        $this->orderComment = $orderComment;

        return $this;
    }

    /**
     * Get orderComment
     *
     * @return string
     */
    public function getOrderComment()
    {
        return $this->orderComment;
    }

    /**
     * Set prodComment
     *
     * @param string $prodComment
     *
     * @return Orders
     */
    public function setProdComment($prodComment)
    {
        $this->prodComment = $prodComment;

        return $this;
    }

    /**
     * Get prodComment
     *
     * @return string
     */
    public function getProdComment()
    {
        return $this->prodComment;
    }

    /**
     * Set clientOrderId
     *
     * @param integer $clientOrderId
     *
     * @return Orders
     */
    public function setClientOrderId($clientOrderId)
    {
        $this->clientOrderId = $clientOrderId;

        return $this;
    }

    /**
     * Get clientOrderId
     *
     * @return integer
     */
    public function getClientOrderId()
    {
        return $this->clientOrderId;
    }

    /**
     * Set clientInfo
     *
     * @param string $clientInfo
     *
     * @return Orders
     */
    public function setClientInfo($clientInfo)
    {
        $this->clientInfo = is_array($clientInfo) ? json_encode($clientInfo) : $clientInfo;
        return $this;
    }

    /**
     * Get clientInfo
     *
     * @return string
     */
    public function getClientInfo()
    {
        return $this->clientInfo;
    }

    /**
     * Add position
     *
     * @param \AppBundle\Entity\Positions $position
     *
     * @return Orders
     */
    public function addPosition(\AppBundle\Entity\Positions $position)
    {
        $position->setOrder($this);
        $position->setNr($this->positions->count() + 1);
        $this->positions->add($position);
        return $this;
    }

    /**
     * Remove position
     *
     * @param \AppBundle\Entity\Positions $position
     */
    public function removePosition(\AppBundle\Entity\Positions $position)
    {
        $this->positions->removeElement($position);
    }

    /**
     * Get positions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPositions()
    {
        return $this->positions;
    }

    public function preUpdate(){
        $this->preUpdateChilds($this->positions);
        $this->saveFieldsValues(['positions', 'uploads']);
        return $this;
    }

    public function postUpdate($em){
        $this->_checkElements('uploads', $em);
        $this->checkUploads();
        $this->_checkElements('positions', $em);
        $this->postUpdateChilds($this->positions, $em);
        return $this;
    }


    // public function saveOldPositions()
    // {
    //     $this->oldPositions = new ArrayCollection();
    //     foreach ($this->positions as $position) {
    //         $position->saveOldUploads();
    //         $this->oldPositions->add($position);
    //     }
    //     return $this->oldPositions;
    // }

    // public function checkPositions($entityManager)
    // {
    //     foreach ($this->oldPositions as $position) {
    //         if (false === $this->positions->contains($position)) {
    //             $entityManager->remove($position);
    //         }
    //     }
    //     foreach ($this->positions as $position) {
    //         $position->checkUpdates($entityManager);
    //     }
    // }

    public function getPositionsDataId()
    {
        $result = [];
        foreach ($this->positions as $position) {
            $result[] = $position->getDataId();
        }
        return $result;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Clients $client
     *
     * @return Orders
     */
    public function setClient(\AppBundle\Entity\Clients $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Clients
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set model
     *
     * @param \AppBundle\Entity\Models $model
     *
     * @return Orders
     */
    public function setModel(\AppBundle\Entity\Models $model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Init model
     *
     *
     * @return Orders
     */
    // public function initModel($controller, $model)
    // {
    //     $this->model = $controller->getFromBase($model, static::$shortNames['childs']['model']);
    //     return $this;
    // }

    /**
     * Get model
     *
     * @return \AppBundle\Entity\Models
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set size
     *
     * @param \AppBundle\Entity\Sizes $size
     *
     * @return Orders
     */
    public function setSize(\AppBundle\Entity\Sizes $size = null)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Init size
     *
     *
     * @return Orders
     */
    // public function initSize($controller, $size)
    // {
    //     $this->size = $controller->getFromBase($size, static::$shortNames['childs']['size']);
    //     return $this;
    // }

    /**
     * Get size
     *
     * @return \AppBundle\Entity\Sizes
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set color
     *
     * @param \AppBundle\Entity\Colors $color
     *
     * @return Orders
     */
    public function setColor(\AppBundle\Entity\Colors $color = null)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Init color
     *
     *
     * @return Orders
     */
    // public function initColor($controller, $color)
    // {
    //     $this->color = $controller->getFromBase($color, static::$shortNames['childs']['color']);
    //     return $this;
    // }

    /**
     * Get color
     *
     * @return \AppBundle\Entity\Colors
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set production.
     *
     * @param \AppBundle\Entity\Productions|null $production
     *
     * @return Orders
     */
    public function setProduction(\AppBundle\Entity\Productions $production = null)
    {
        if (is_null($production)){
			if($this->setStatus(3)){
				$this->production= null;
			}
		}else{
            if (is_null($this->production) && $this->setStatus(4)) {
                $this->genProducts($production);
                $this->production = $production;
            }
        }
        return $this;
    }

    public function getProducts(){
        $products= new ArrayCollection();
        foreach($this->positions as $pos){
            foreach ($pos->getProducts() as $p) {
                $products->add($p);
            }
        }
        return $products;
    }
    /**
     * Get production.
     *
     * @return \AppBundle\Entity\Productions|null
     */
    public function getProduction()
    {
        return $this->production;
    }

    /**
     * Set delivery.
     *
     * @param \AppBundle\Entity\Deliveries|null $delivery
     *
     * @return Orders
     */
    public function setDelivery(\AppBundle\Entity\Deliveries $delivery = null)
    {
        if (is_null($delivery)){
            $this->delivery= null;
		}else if (!is_null($this->production) && is_null($this->delivery)) {
            $this->delivery = $delivery;
        }
        return $this;
    }

    /**
     * Get delivery.
     *
     * @return \AppBundle\Entity\Deliveries|null
     */
    public function getDelivery()
    {
        return $this->delivery;
    }
    
    /**
     * Set invoice.
     *
     * @param \AppBundle\Entity\Invoices|null $invoice
     *
     * @return Orders
     */
    public function setInvoice(\AppBundle\Entity\Invoices $invoice = null)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice.
     *
     * @return \AppBundle\Entity\Invoices|null
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Add upload
     *
     * @param \AppBundle\Entity\Uploads $upload
     *
     * @return Orders
     */
    public function addUpload(\AppBundle\Entity\Uploads $upload)
    {
        $this->uploads[] = $upload;

        return $this;
    }

    /**
     * Remove upload
     *
     * @param \AppBundle\Entity\Uploads $upload
     */
    public function removeUpload(\AppBundle\Entity\Uploads $upload)
    {
        $this->uploads->removeElement($upload);
    }

    /**
     * Get uploads
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUploads()
    {
        return $this->uploads;
    }

    /**
     * Add note
     *
     * @param \AppBundle\Entity\Notes $note
     *
     * @return Orders
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

    public function getSuccessData($type)
    {
        $data = parent::getSuccessData($type);
        switch ($type) {
            case 'created':
            case 'updated':
                $data['positions'] = $this->getPositionsDataId();
                // no break
            case 'removed':
            default:
        }
        return $data;
    }

    public function getDataDelete()
    {
        $data = array_replace_recursive(
            parent::getDataDelete(),
            $this->getFieldsStr(['number', 'created'])
        );
        $data['client'] = $this->getClient()->getName();
        return $data;
    }

    public function importMedos($order, $options = [])
    {
        $imp = [
            'clientOrderId' => $order['clientOrderId'],
            'created' => $this->getStrCreated(true),
            'status' => 'ok',
            'messages' => [],
            'positions' => [],
            'count' => 0
        ];
        $def_params = [];
        $positions = [];
        foreach ($options['map_params'] as $n => $mn) {
            $def_params[$n] = [];
        }
        if (count($order['items']) > 0) {
            foreach ($order['items'] as $item) {
                $position = new Positions(['controller' => $this->controller]);
                $p_imp = $position->importMedos($item, $options);
                $imp['status'] = $this->setValidationStatus($p_imp['status'], $imp['status']);
                $imp['positions'][] = $p_imp;
                if ($p_imp['status'] !== 'error') {
                    foreach ($options['map_params'] as $n => $mn) {
                        if (isset($def_params[$n][$item[$mn]])) {
                            $def_params[$n][$item[$mn]] += $item['quantity'];
                        } else {
                            $def_params[$n][$item[$mn]] = 0;
                        }
                    }
                    $positions[] = $position;
                }
            }
            if ($imp['status'] != 'error') {
                $imp['count'] = count($positions);
                for ($i = 0 ; $i < $imp['count']; $i++) {
                    $positions[$i]->setNr($i + 1);
                    $this->addPosition($positions[$i]);
                }
                $imp['messages'][] = $this->genMessage('import_positions_success', null, $imp['count']);
            } else {
                $imp['messages'][] = $this->genMessage('import_positions_error');
            }
        } else {
            $imp['status'] = 'error';
            $imp['messages'][] = $this->genMessage('import_positions_empty');
        }
        foreach ($def_params as $n => $values) {
            $max = $this->getMax($values);
            if (array_key_exists($max, $options[$n])) {
                $set_fn = 'set' . ucfirst($n);
                $this->$set_fn($this->controller->getEntityManager()->getReference(self::getChildEntityClass($n), $options[$n][$max]));
            } else {
                $imp['status'] = 'error';
                $imp['messages'][] = $this->genMessage('import_default_' . $n . '_error');
            }
        }
        if ($imp['status'] != 'error') {
            $this->setCreated(new \DateTime($order['created']));
            $this->setApproved(new \DateTime(Utils::deep_array_value('delivery-generated', $order, 'now')));
            $this->setClientOrderId($order['clientOrderId']);
            $this->setClientNumber(Utils::deep_array_value('delivery-_purchaseMedosNumber', $order, '') . '/' . $order['clientOrderId']);
            $this->setStatus(3);
            $this->setClientInfo($imp);
            $this->summary($options);
        }
        return $imp;
    }

    private function getMax($array)
    {
        $max = null;
        foreach ($array as $k => $v) {
            if (isset($max)) {
                if ($v > $array[$max]) {
                    $max = $k;
                }
            } else {
                $max = $k;
            }
        }
        return $max;
    }

    public function getNr()
    {
        return $this->intNr ? $this->intNr : intval($this->number);
    }

    /**
     * Generate products
     *
     */
    public function genProducts(\AppBundle\Entity\Productions $production)
    {
        $products = [];
        $productNumber = $this->getNumber() . '-';
        $orderQuantity = $this->getQuantity();
        $prefixLabel = $this->getNr() . ' ' . $this->getClient()->getCode() . ' ';
        $nrprod = 1;

        foreach ($this->positions as $position) {
            $sufixLabel = ' ' . $position->getDescription();
            $clientInfo = $position->getClientInfo(true);
            $clientLabels = Utils::deep_array_value('labels', $clientInfo, []);
            $posQuantity = $position->getQuantity();
            for ($i = 0; $i < $posQuantity; $i++, $nrprod++) {
                $product = new Products();
                $product->setNumber($productNumber . $nrprod . '/' . $this->quantity);
                $product->setLabel($prefixLabel . $nrprod . $sufixLabel);
                $product->setClientLabel(Utils::deep_array_value($i, $clientLabels));

                $position->addProduct($product);
                $production->addProduct($product);
                array_push($products, $product);
            }
        }
        return $this;
    }

	public function removeProducts(){
		foreach($this->positions as $position){
			$position->removeProducts();
		}
		return $this;
	}


    /**
     * @ORM\PrePersist
     */
    public function prePersistOrders()
    {
        if (is_null($this->created)) {
            $this->created = new \DateTime();
        }
        $this->approve();
        $this->summary();
        // $this->calcProgress();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdateOrders()
    {
        $this->approve();
        $this->summary();
    }


}
