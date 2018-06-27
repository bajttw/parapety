<?php

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Utils\Utils;

namespace AppBundle\Entity;

/**
 * Invoices
 */
class Invoices extends AppEntity
{
    const en = 'invoices';
    const ec = 'Invoices';
    const idPrototype = '__iid__';

//  <editor-fold defaultstate="collapsed" desc="Fields utils">
    public static $shortNames = [
        'id' => 'id',
        'created' => 'cr',
        'term' => 't',
        'number' => 'nr',
        'client' => 'cl',
        'childs' =>[
            'client' => 'Clients',
            'orders' => 'Orders',
            'invoiceItems' => 'InvoiceItems'
        ]
    ];

    public static function getFields($type = null)
    {
        switch ($type) {
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
                    [
                        'name' => 'created',
                        'prefix' => 'DATE_FORMAT(',
                        'sufix' => ", '%Y-%m-%d')"
                    ],
                    [
                        'name' => 'term',
                        'prefix' => 'DATE_FORMAT(',
                        'sufix' => ", '%Y-%m-%d')"
                    ]
                ];
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
                $fields = ['created', 'term'];
            case 'update':
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
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime|null
     */
    private $term;

    /**
     * @var string
     */
    private $number;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $orders;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $invoiceItems;

    /**
     * @var \AppBundle\Entity\Clients
     */
    private $client;

// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Variables extra">

// </editor-fold>

    /**
     * Constructor
     */
    public function __construct($options = [])
    {
        $this->orders = new ArrayCollection();
        $this->invoiceItems = new ArrayCollection();
        parent::__construct($options);       
    }

    // <editor-fold defaultstate="collapsed" desc="Fields functions">

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Invoices
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set term.
     *
     * @param \DateTime|null $term
     *
     * @return Invoices
     */
    public function setTerm($term = null)
    {
        $this->term = $term;

        return $this;
    }

    /**
     * Get term.
     *
     * @return \DateTime|null
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Set number.
     *
     * @param string $number
     *
     * @return Invoices
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Add order.
     *
     * @param \AppBundle\Entity\Orders $order
     *
     * @return Invoices
     */
    public function addOrder(\AppBundle\Entity\Orders $order)
    {
        $this->orders[] = $order;

        return $this;
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
        return $this->orders->removeElement($order);
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

    /**
     * Add invoiceItem.
     *
     * @param \AppBundle\Entity\InvoiceItems $invoiceItem
     *
     * @return Invoices
     */
    public function addInvoiceItem(\AppBundle\Entity\InvoiceItems $invoiceItem)
    {
        $this->invoiceItems[] = $invoiceItem;

        return $this;
    }

    /**
     * Remove invoiceItem.
     *
     * @param \AppBundle\Entity\InvoiceItems $invoiceItem
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeInvoiceItem(\AppBundle\Entity\InvoiceItems $invoiceItem)
    {
        return $this->invoiceItems->removeElement($invoiceItem);
    }

    /**
     * Get invoiceItems.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoiceItems()
    {
        return $this->invoiceItems;
    }

    /**
     * Set client.
     *
     * @param \AppBundle\Entity\Clients|null $client
     *
     * @return Invoices
     */
    public function setClient(\AppBundle\Entity\Clients $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client.
     *
     * @return \AppBundle\Entity\Clients|null
     */
    public function getClient()
    {
        return $this->client;
    }
// </editor-fold>
    
}
