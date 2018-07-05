<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
		* Defectives
		*/
class Defectives extends AppEntity{
    const en='defectives';
    const ec='Defectives';
    const idPrototype = '__did__';

    public static $shortNames=[
        'id' => 'id',
        'description' => 'd',
        'status' => 's',
        'childs' => [
            'product' => 'Products',
            'complaint' => 'Complaints',
            'uploads' => "Uploads",
            'notes' => "Notes"
        ]
    ];

    /**
		* @var integer
		*/
    private $id;

    /**
		* @var string
		*/
    private $description;

    /**
		* @var integer
		*/
    private $status = 1;

    /**
		* @var \AppBundle\Entity\Products
		*/
    private $product;

    /**
		* @var \AppBundle\Entity\Complaints
		*/
    private $complaint;

    /**
		* @var \Doctrine\Common\Collections\Collection
		*/
    private $uploads;

    /**
		* @var \Doctrine\Common\Collections\Collection
		*/
    private $notes;

    /**
		* Constructor
		*/
    public function __construct(array $options=[])
    {
      parent::__construct($options);
      $this->uploads = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

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
		* Set description
		*
		* @param string $description
		*
		* @return Defectives
		*/
    public function setDescription(?string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
		* Get description
		*
		* @return string
		*/
    public function getDescription():?string
    {
        return $this->description;
    }

    /**
		* Set status
		*
		* @param integer $status
		*
		* @return Defectives
		*/
    public function setStatus($status)
    {
        $this->status = $status;

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
		* Set product
		*
		* @param \AppBundle\Entity\Products $product
		*
		* @return Defectives
		*/
    public function setProduct(\AppBundle\Entity\Products $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
		* Get product
		*
		* @return \AppBundle\Entity\Products
		*/
    public function getProduct()
    {
        return $this->product;
    }

    /**
		* Set complaint
		*
		* @param \AppBundle\Entity\Complaints $complaint
		*
		* @return Defectives
		*/
    public function setComplaint(\AppBundle\Entity\Complaints $complaint = null)
    {
        $this->complaint = $complaint;

        return $this;
    }

    /**
		* Get complaint
		*
		* @return \AppBundle\Entity\Complaints
		*/
    public function getComplaint()
    {
        return $this->complaint;
    }

    /**
		* Add upload
		*
		* @param \AppBundle\Entity\Uploads $upload
		*
		* @return Defectives
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
		* @return Defectives
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
}
