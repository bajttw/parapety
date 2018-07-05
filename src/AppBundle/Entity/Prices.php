<?php

namespace AppBundle\Entity;
use \AppBundle\Entity\PriceListItems;
use \AppBundle\Entity\PriceLists;
/**
 * Prices
 */
class Prices extends AppEntity
{
    const en = 'Prices';
    const ec = 'prices';
    const idPrototype = '__pid__';

//  <editor-fold defaultstate="collapsed" desc="Fields utils">
    public static $shortNames = [
        'id' => 'id',
        'value' => 'val',
        'priceList' => 'pl',
        'priceListItem' => 'pli',
        'childs' =>[
            'priceList' => 'PriceLists',
            'priceListItem' => 'PriceListItems'
        ]
    ];
// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Variables">
    /**
     * @var int
     */
    private $id;

    /**
     * @var float|null
     */
    private $value = 0;

    /**
     * @var \AppBundle\Entity\PriceLists
     */
    private $priceList;

    /**
     * @var \AppBundle\Entity\PriceListItems
     */
    private $priceListItem;
// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Variables extra">
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Fields functions">

    /**
     * Get id.
     *
     * @return int
     */
    public function getId():?int
    {
        return $this->id;
    }

    /**
     * Set value.
     *
     * @param float|null $value
     *
     * @return Prices
     */
    public function setValue(float $value = null)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return float|null
     */
    public function getValue():?float
    {
        return $this->value;
    }

    /**
     * Set priceList.
     *
     * @param \AppBundle\Entity\PriceLists|null $priceList
     *
     * @return Prices
     */
    public function setPriceList(\AppBundle\Entity\PriceLists $priceList = null)
    {
        $this->priceList = $priceList;

        return $this;
    }

    /**
     * Get priceList.
     *
     * @return \AppBundle\Entity\PriceLists|null
     */
    public function getPriceList():?PriceLists
    {
        return $this->priceList;
    }

    /**
     * Set priceListItem.
     *
     * @param \AppBundle\Entity\PriceListItems|null $priceListItem
     *
     * @return Prices
     */
    public function setPriceListItem(\AppBundle\Entity\PriceListItems $priceListItem = null)
    {
        $this->priceListItem = $priceListItem;

        return $this;
    }

    /**
     * Get priceListItem.
     *
     * @return \AppBundle\Entity\PriceListItems|null
     */
    public function getPriceListItem():?PriceListItems
    {
        return $this->priceListItem;
    }
// </editor-fold>
    
}
