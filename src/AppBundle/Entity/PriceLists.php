<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Utils\Utils;

/**
 * PriceLists
 */
class PriceLists extends AppEntity
{
    const en = 'pricelists';
    const ec = 'PriceLists';
    const idPrototype = '__plid__';

//  <editor-fold defaultstate="collapsed" desc="Fields utils">
    public static $dicNames = [
        'id' => 'v',
        'title' => 'n',
        'description' => 'd'
    ];

    public static $shortNames = [
        'id' => 'id',
        'title' => 't',
        'description' => 'd',
        'start' => 's',
        'end' => 'end',
        'childs' =>[
            'prices' => 'Prices',
            'clients' => 'Clients',
            'clientsGroups' => 'ClientsGroups',
            'notes' => 'Notes'
        ]
    ];

    
    public function getSuccessFields($type)
    {
        $fields = [];
        switch ($type) {
            case 'create':
            case 'update':
                $fields = ['start', 'end'];
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
                $fields = ['title', 'start'];
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
    private $title;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime|null
     */
    private $end;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $prices;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $clients;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $clientsGroups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $notes;

// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Variables extra">
// </editor-fold>

    /**
     * Constructor
     */
    public function __construct($options = [])
    {
        $this->prices = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->clientsGroups = new ArrayCollection();
        $this->notes = new ArrayCollection();
        parent::__construct($options);       
    }

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
     * Set title.
     *
     * @param string $title
     *
     * @return PriceLists
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return PriceLists
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription():?string
    {
        return $this->description;
    }

    /**
     * Set start.
     *
     * @param \DateTime $start
     *
     * @return PriceLists
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start.
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end.
     *
     * @param \DateTime|null $end
     *
     * @return PriceLists
     */
    public function setEnd($end = null)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end.
     *
     * @return \DateTime|null
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Add price.
     *
     * @param \AppBundle\Entity\Prices $price
     *
     * @return PriceLists
     */
    public function addPrice(\AppBundle\Entity\Prices $price)
    {
        $price->setPriceList($this);
        $this->prices->add($price);
        return $this;
    }

    /**
     * Remove price.
     *
     * @param \AppBundle\Entity\Prices $price
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePrice(\AppBundle\Entity\Prices $price)
    {
        return $this->prices->removeElement($price);
    }

    /**
     * Get prices.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * Add client.
     *
     * @param \AppBundle\Entity\Clients $client
     *
     * @return PriceLists
     */
    public function addClient(\AppBundle\Entity\Clients $client)
    {
        $client->addPriceList($this);
        $this->clients[] = $client;

        return $this;
    }

    /**
     * Remove client.
     *
     * @param \AppBundle\Entity\Clients $client
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeClient(\AppBundle\Entity\Clients $client)
    {
        $client->removePriceList($this);
        return $this->clients->removeElement($client);
    }

    /**
     * Get clients.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * Add clientsGroup.
     *
     * @param \AppBundle\Entity\ClientsGroups $clientsGroup
     *
     * @return PriceLists
     */
    public function addClientsGroup(\AppBundle\Entity\ClientsGroups $clientsGroup)
    {
        $clientsGroup->addPriceList($this);
     
        $this->clientsGroups[] = $clientsGroup;

        return $this;
    }

    /**
     * Remove clientsGroup.
     *
     * @param \AppBundle\Entity\ClientsGroups $clientsGroup
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeClientsGroup(\AppBundle\Entity\ClientsGroups $clientsGroup)
    {
        $clientsGroup->removePriceList($this);
        return $this->clientsGroups->removeElement($clientsGroup);
    }

    /**
     * Get clientsGroups.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClientsGroups()
    {
        return $this->clientsGroups;
    }

    /**
     * Add note.
     *
     * @param \AppBundle\Entity\Notes $note
     *
     * @return PriceLists
     */
    public function addNote(\AppBundle\Entity\Notes $note)
    {
        $this->notes[] = $note;

        return $this;
    }

    /**
     * Remove note.
     *
     * @param \AppBundle\Entity\Notes $note
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeNote(\AppBundle\Entity\Notes $note)
    {
        return $this->notes->removeElement($note);
    }

    /**
     * Get notes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }
// </editor-fold>

    public function getDataDelete()
    {
        $data = parent::getDataDelete();
        $data['title']=$this->getTitle();
        $data['start']=$this->toStrData($this->getStart());
        return $data;
    }
    /**
     * @ORM\PrePersist
     */
    public function prePersistPriceLists()
    {
        if (is_null($this->start)) {
            $this->start = new \DateTime();
        }
    }
}
