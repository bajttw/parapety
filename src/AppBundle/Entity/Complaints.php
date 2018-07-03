<?php

namespace AppBundle\Entity;

/**
    * Complaints
    */
class Complaints extends AppEntity{
    const en='complaints';
    const ec='Complaints';
    const idPrototype = '__cid__';

    public static $shortNames=[
        'id' => 'id',
        'created' => 'cr',
        'dateStart' => 'ds',
        'number' => 'nr', 
        'quantity' => 'q',
        'area' => 'a',
        'status' => 's',
        'comment' => 'c',
        'drakoComment' => 'dc',
        'clientInfo' => 'ci',
        'childs' => [
            'defectives' => 'Defectives',
            'client' => "Clients",
            'notes' => "Notes"
        ]
    ];

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
    private $dateStart;

    /**
        * @var string
        */
    private $number;

    /**
        * @var integer
        */
    private $quantity = 1;

    /**
        * @var float
        */
    private $area = 0;

    /**
        * @var integer
        */
    private $status = 1;

    /**
        * @var string
        */
    private $comment;

    /**
        * @var string
        */
    private $drakoComment;

    /**
        * @var string
        */
    private $clientInfo;

    /**
        * @var \Doctrine\Common\Collections\Collection
        */
    private $defectives;

    /**
        * @var \AppBundle\Entity\Clients
        */
    private $client;

    /**
        * @var \Doctrine\Common\Collections\Collection
        */
    private $notes;

    /**
        * Constructor
        */
    public function __construct($options=[]){
        $this->defectives = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notes = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct($options);
    }

    /**
        * Get id
        *
        * @return integer
        */
    public function getId():int{
        return $this->id;
    }

    /**
        * Set created
        *
        * @param \DateTime $created
        *
        * @return Complaints
        */
    public function setCreated($created){
        $this->created = $created;
        return $this;
    }

    /**
        * Get created
        *
        * @return \DateTime
        */
    public function getCreated(){
        return $this->created;
    }

    /**
        * Set dateStart
        *
        * @param \DateTime $dateStart
        *
        * @return Complaints
        */
    public function setDateStart($dateStart){
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
        * Get dateStart
        *
        * @return \DateTime
        */
    public function getDateStart(){
        return $this->dateStart;
    }

    /**
        * Set number
        *
        * @param string $number
        *
        * @return Complaints
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
        * Set quantity
        *
        * @param integer $quantity
        *
        * @return Complaints
        */
    public function setQuantity($quantity){
        $this->quantity = $quantity;

        return $this;
    }

    /**
        * Get quantity
        *
        * @return integer
        */
    public function getQuantity(){
        return $this->quantity;
    }

    /**
        * Set area
        *
        * @param float $area
        *
        * @return Complaints
        */
    public function setArea($area){
        $this->area = $area;

        return $this;
    }

    /**
        * Get area
        *
        * @return float
        */
    public function getArea(){
        return $this->area;
    }

    /**
        * Set status
        *
        * @param integer $status
        *
        * @return Complaints
        */
    public function setStatus($status){
        $this->status = $status;

        return $this;
    }

    /**
        * Get status
        *
        * @return integer
        */
    public function getStatus(){
        return $this->status;
    }

    /**
        * Set comment
        *
        * @param string $comment
        *
        * @return Complaints
        */
    public function setComment($comment){
        $this->comment = $comment;

        return $this;
    }

    /**
        * Get comment
        *
        * @return string
        */
    public function getComment(){
        return $this->comment;
    }

    /**
        * Set drakoComment
        *
        * @param string $drakoComment
        *
        * @return Complaints
        */
    public function setDrakoComment($drakoComment){
        $this->drakoComment = $drakoComment;

        return $this;
    }

    /**
        * Get drakoComment
        *
        * @return string
        */
    public function getDrakoComment(){
        return $this->drakoComment;
    }

    /**
        * Set clientInfo
        *
        * @param string $clientInfo
        *
        * @return Complaints
        */
    public function setClientInfo($clientInfo){
        $this->clientInfo = $clientInfo;

        return $this;
    }

    /**
        * Get clientInfo
        *
        * @return string
        */
    public function getClientInfo(){
        return $this->clientInfo;
    }

    /**
        * Add defective
        *
        * @param \AppBundle\Entity\Defectives $defective
        *
        * @return Complaints
        */
    public function addDefective(\AppBundle\Entity\Defectives $defective){
        $this->defectives[] = $defective;

        return $this;
    }

    /**
        * Remove defective
        *
        * @param \AppBundle\Entity\Defectives $defective
        */
    public function removeDefective(\AppBundle\Entity\Defectives $defective){
        $this->defectives->removeElement($defective);
    }

    /**
        * Get defectives
        *
        * @return \Doctrine\Common\Collections\Collection
        */
    public function getDefectives(){
        return $this->defectives;
    }

    /**
        * Set client
        *
        * @param \AppBundle\Entity\Clients $client
        *
        * @return Complaints
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
        * @return Complaints
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

    /**
        * @ORM\PrePersist
        */
    public function prePersistComplaints(){
        // Add your code here
    }

    /**
        * @ORM\PostPersist
        */
    public function postPersistComplaints(){
        // Add your code here
    }

    /**
        * @ORM\PreUpdate
        */
    public function preUpdateComplaints(){
        // Add your code here
    }

}
