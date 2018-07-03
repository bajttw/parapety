<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;


/**
 * PriceListItems
 */
class PriceListItems extends AppEntity
{
    const en = 'pricelistitems';
    const ec = 'PriceListItems';
    const idPrototype = '__pliid__';

 //  <editor-fold defaultstate="collapsed" desc="Fields utils">
    public static $dicNames=[
        'id' => 'v',
        'name' => 'n',
        'symbol' => 's',
        'price' => 'price'

    ];

    public static $shortNames = [
        'id' => 'id',
        'name' => 'n',
        'symbol' => 'sm',
        'price' => 'p',
        'active' => 'a',
        'size' => 's',
        'color' => 'c',
        'childs' => [
            'size' => 'Sizes',
            'color' => 'Colors'
        ]
    ];

    public static function getFields($type = null)
    {
        switch ($type) {
            case "uniques":
                $fields = [
                    'id',
                    [
                        'name' => 'size',
                        'joinField' => [
                            ['name' => 'id'],
                        ]
                    ],
                    [
                        'name' => 'color',
                        'joinField' => [
                            ['name' => 'id'],
                        ]

                    ]
                ];
            break;
            case 'data':
            case 'dic':
                $fields = [
                    'id',
                    'name',
                    'symbol',
                    'price'
                ];
       
            break;
            default:
                $fields = [
                    'id',
                    'name',
                    'symbol',
                    'price',
                    'active',
                    [
                        'name' => 'size',
                        'joinField' => [
                            ['name' => 'id'],
                            ['name' => 'name']
                        ]
                    ],
                    [
                        'name' => 'color',
                        'joinField' => [
                            ['name' => 'id'],
                            ['name' => 'name']
                        ]

                    ]
                ];
        }
        return $fields;
    }

    public function getSuccessFields($type)
    {
        $fields = [];
        switch ($type) {
            case 'add':
            case 'create':
                $fields = ['name', 'symbol'];
            break;
            case 'update':
                $fields = ['approved'];
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
     * @var string
     */
    private $name;

    /**
     * @var string|null
     */
    private $symbol;

    /**
     * @var float|null
     */
    private $price = 0;

    /**
     * @var bool
     */
    private $active = true;

    /**
     * @var \AppBundle\Entity\Sizes
     */
    private $size;

    /**
     * @var \AppBundle\Entity\Colors
     */
    private $color;

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
     * Set name.
     *
     * @param string $name
     *
     * @return PriceListItems
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName():?string
    {
        return $this->name;
    }

    /**
     * Set symbol.
     *
     * @param string|null $symbol
     *
     * @return PriceListItems
     */
    public function setSymbol($symbol = null)
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * Get symbol.
     *
     * @return string|null
     */
    public function getSymbol()
    {
        return $this->symbol;
    }
    /**
     * Set price.
     *
     * @param float|null $price
     *
     * @return PriceListItems
     */
    public function setPrice($price = null)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return float|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return PriceListItems
     */
    public function setActive(bool $active)
    {
        $this->active = $active;

        return $this;
    }

    public function updateActive()
    {
        $this->active= $this->size->getActive() && $this->color->getActive();
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function getActive():bool
    {
        return $this->active;
    }
    
    /**
     * Set size.
     *
     * @param \AppBundle\Entity\Sizes|null $size
     *
     * @return PriceListItems
     */
    public function setSize(\AppBundle\Entity\Sizes $size = null)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size.
     *
     * @return \AppBundle\Entity\Sizes|null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set color.
     *
     * @param \AppBundle\Entity\Colors|null $color
     *
     * @return PriceListItems
     */
    public function setColor(\AppBundle\Entity\Colors $color = null)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return \AppBundle\Entity\Colors|null
     */
    public function getColor()
    {
        return $this->color;
    }
// </editor-fold>
    public function getUnique()
    {
        return $this->size->getId() . '_' . $this->color->getId();
    }


    /**
     * @ORM\PrePersist
     */
    public function prePersistPriceListItem()
    {
        $this->updateActive();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdatePriceListItem()
    {
        $this->updateActive();
    }



}
