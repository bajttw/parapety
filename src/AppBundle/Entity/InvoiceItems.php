<?php

namespace AppBundle\Entity;

/**
 * InvoiceItems
 */
class InvoiceItems extends AppEntity
{
    const en = 'invoiceitems';
    const ec = 'InvoiceItems';
    const idPrototype = '__iiid__';

 //  <editor-fold defaultstate="collapsed" desc="Fields utils">

    public static $shortNames = [
        'id' => 'id',
        'nr' => 'nr',
        'name' => 'n',
        'value' => 'v',
        'quantity' => 'q',
        'childs' =>[
            'invoice' => 'Invoices',
            'price' => 'Prices'
        ]
    ];
// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Utilities">    
// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Variables">
    /**
     * @var int
     */
    private $id;

    /**
     * @var int|null
     */
    private $nr = 0;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var float|null
     */
    private $value = 0;

    /**
     * @var float|null
     */
    private $quantity = 0;

    /**
     * @var \AppBundle\Entity\Invoices
     */
    private $invoice;

    /**
     * @var \AppBundle\Entity\Prices
     */
    private $price;
// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Variables extra">


// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Fields functions">

    /**
     * Get id.
     *
     * @return int
     */
    public function getId():int
    {
        return $this->id;
    }

    /**
     * Set nr.
     *
     * @param int|null $nr
     *
     * @return InvoiceItems
     */
    public function setNr($nr = null)
    {
        $this->nr = $nr;

        return $this;
    }

    /**
     * Get nr.
     *
     * @return int|null
     */
    public function getNr()
    {
        return $this->nr;
    }

    /**
     * Set name.
     *
     * @param string|null $name
     *
     * @return InvoiceItems
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName():?string
    {
        return $this->name;
    }

    /**
     * Set value.
     *
     * @param float|null $value
     *
     * @return InvoiceItems
     */
    public function setValue($value = null)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return float|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set quantity.
     *
     * @param float|null $quantity
     *
     * @return InvoiceItems
     */
    public function setQuantity($quantity = null)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return float|null
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set invoice.
     *
     * @param \AppBundle\Entity\Invoices|null $invoice
     *
     * @return InvoiceItems
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
     * Set price.
     *
     * @param \AppBundle\Entity\Prices|null $price
     *
     * @return InvoiceItems
     */
    public function setPrice(\AppBundle\Entity\Prices $price = null)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return \AppBundle\Entity\Prices|null
     */
    public function getPrice()
    {
        return $this->price;
    }
// </editor-fold>
    
}
