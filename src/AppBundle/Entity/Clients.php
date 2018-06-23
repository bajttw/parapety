<?php
namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Clients
 */
class Clients extends AppEntity
{
    const en = 'clients';
    const ec = 'Clients';
    const emptyId = '__cid__';

    //  <editor-fold defaultstate="collapsed" desc="Fields utils">
    public static $dicNames = [
        'id' => 'v',
        'code' => 'cc',
        'name' => 'n',
        'nip' => 'd'
    ];

    public static $shortNames = [
        'id' => 'id',
        'code' => 'cc',
        'name' => 'n',
        'street' => 'as',
        'zipCode' => 'az',
        'city' => 'ac',
        'nip' => 'nip',
        'contact' => 'c',
        'tel' => 't',
        'mobile' => 'm',
        'email' => 'em',
        'active' => 'a',
        'regular' => 'r',
        'description' => 'd',
        'childs' => [
            'clientGroups' => 'ClientsGroups',
            'users' => "Users",
            'settings' => "Settings",
            'priceLists' => "PriceLists",
            'orders' => "Orders",
            'deliveries' => "Deliveries",
            "invoices" => 'Invoices',
            'notes' => 'Notes'
        ]

    ];

    public static function getFields($type = null) {
        switch($type){
            case 'serviceorders':
                $fields=[ 'id', 'code', 'name', 'street', 'zipCode', 'city', 'tel', 'mobile' ];
            break;
            case 'packages':
                $fields=['id', 'code', 'name'];
            break;
            case 'filter':
                $fields=['id', 'name'];
            break;
            case 'settings':
                $fields=['id', 'name'];
            break;
            case 'data':
            case 'dic':
            default:
                $fields= ['id', 'code', 'name', 'nip'];
        }
        return $fields;
    }
 // </editor-fold>       

 //  <editor-fold defaultstate="collapsed" desc="Variables">    
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $code = 'N';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $zipCode;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $nip;

    /**
     * @var string
     */
    private $contact;

    /**
     * @var string
     */
    private $tel;

    /**
     * @var string
     */
    private $mobile;

    /**
     * @var string
     */
    private $email;

    /**
     * @var boolean
     */
    private $active = true;

    /**
     * @var boolean
     */
    private $regular = false;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $clientGroups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $users;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $settings;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $priceLists;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $orders;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $deliveries;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $invoices;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $notes;
 // </editor-fold>       

    /**
     * Constructor
     */
    public function __construct($options=[])
    {
        $this->clientGroups = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->settings = new ArrayCollection();
        $this->priceLists = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->deliveries = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->notes = new ArrayCollection();
        parent::__construct($options);
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getData($jsonEncode=true, $options=[]){
        return parent::getData($jsonEncode, array_replace([ 
                'shortNames' => 'dic'
            ],
            $options
        ));
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
     * Set code
     *
     * @param string $code
     *
     * @return Clients
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Clients
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set street
     *
     * @param string $street
     *
     * @return Clients
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return Clients
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Clients
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set nip
     *
     * @param string $nip
     *
     * @return Clients
     */
    public function setNip($nip)
    {
        $this->nip = $nip;
        return $this;
    }

    /**
     * Get nip
     *
     * @return string
     */
    public function getNip()
    {
        return $this->nip;
    }

    /**
     * Set contact
     *
     * @param string $contact
     *
     * @return Clients
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set tel
     *
     * @param string $tel
     *
     * @return Clients
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     *
     * @return Clients
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Clients
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Clients
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set regular
     *
     * @param boolean $regular
     *
     * @return Clients
     */
    public function setRegular($regular)
    {
        $this->regular = $regular;

        return $this;
    }

    /**
     * Get regular
     *
     * @return boolean
     */
    public function getRegular()
    {
        return $this->regular;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Clients
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add clientGroup.
     *
     * @param \AppBundle\Entity\ClientsGroups $clientGroup
     *
     * @return Clients
     */
    public function addClientGroup(\AppBundle\Entity\ClientsGroups $clientGroup)
    {
        $this->clientGroups[] = $clientGroup;

        return $this;
    }

    /**
     * Remove clientGroup.
     *
     * @param \AppBundle\Entity\ClientsGroups $clientGroup
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeClientGroup(\AppBundle\Entity\ClientsGroups $clientGroup)
    {
        return $this->clientGroups->removeElement($clientGroup);
    }

    /**
     * Get clientGroups.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClientGroups()
    {
        return $this->clientGroups;
    }

    /**
     * Add user
     *
     * @param \AppBundle\Entity\Users $user
     *
     * @return Clients
     */
    public function addUser(\AppBundle\Entity\Users $user)
    {
        $user->setClient($this);
        $this->users->add($user);
        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\Users $user
     */
    public function removeUser(\AppBundle\Entity\Users $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add setting
     *
     * @param \AppBundle\Entity\Settings $setting
     *
     * @return Clients
     */
    public function addSetting(\AppBundle\Entity\Settings $setting)
    {
        $this->settings[] = $setting;

        return $this;
    }

    /**
     * Remove setting
     *
     * @param \AppBundle\Entity\Settings $setting
     */
    public function removeSetting(\AppBundle\Entity\Settings $setting)
    {
        $this->settings->removeElement($setting);
    }

    /**
     * Get settings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Add priceList
     *
     * @param \AppBundle\Entity\PriceLists $priceList
     *
     * @return Clients
     */
    public function addPriceList(\AppBundle\Entity\PriceLists $priceList)
    {
        $this->priceLists[] = $priceList;

        return $this;
    }

    /**
     * Remove priceList
     *
     * @param \AppBundle\Entity\PriceLists $priceList
     */
    public function removePriceList(\AppBundle\Entity\PriceLists $priceList)
    {
        $this->priceLists->removeElement($priceList);
    }

    /**
     * Get priceLists
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPriceLists()
    {
        return $this->priceLists;
    }
        
    /**
     * Add order
     *
     * @param \AppBundle\Entity\Orders $order
     *
     * @return Clients
     */
    public function addOrder(\AppBundle\Entity\Orders $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \AppBundle\Entity\Orders $order
     */
    public function removeOrder(\AppBundle\Entity\Orders $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Add delivery
     *
     * @param \AppBundle\Entity\Deliveries $delivery
     *
     * @return Clients
     */
    public function addDelivery(\AppBundle\Entity\Deliveries $delivery)
    {
        $this->deliveries[] = $delivery;

        return $this;
    }

    /**
     * Remove delivery
     *
     * @param \AppBundle\Entity\Deliveries $delivery
     */
    public function removeDelivery(\AppBundle\Entity\Deliveries $delivery)
    {
        $this->deliveries->removeElement($delivery);
    }

    /**
     * Get deliveries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDeliveries()
    {
        return $this->deliveries;
    }

    /**
     * Add invoice.
     *
     * @param \AppBundle\Entity\Invoices $invoice
     *
     * @return Clients
     */
    public function addInvoice(\AppBundle\Entity\Invoices $invoice)
    {
        $this->invoices[] = $invoice;

        return $this;
    }

    /**
     * Remove invoice.
     *
     * @param \AppBundle\Entity\Invoices $invoice
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeInvoice(\AppBundle\Entity\Invoices $invoice)
    {
        return $this->invoices->removeElement($invoice);
    }

    /**
     * Get invoices.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * Add note
     *
     * @param \AppBundle\Entity\Notes $note
     *
     * @return Clients
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


//
//    public function genNumber(){
//        // $nr=$this->nrOrder > 0 ? $this->nrOrder : 1;
//        // $numberGenerator=$this->nrOrderGenerator ? json_decode($this->nrOrderGenerator, true) : [] ;
//        // return $this->generateNumber($numberGenerator, $nr);
//        return 1;
//    }
//
//    public function genClientNumber(){
//        // $nr=$this->clientNrOrder > 0 ? $this->clientNrOrder : 1;
//        // $numberGenerator=$this->clientNrOrderGenerator ? json_decode($this->clientNrOrderGenerator, true) : [] ;
//        // return $this->generateNumber($numberGenerator, $nr);
//        return 1;
//    }
//
//    public function nextNr(){
//        // if( $this->nrOrder > 0){
//        //     $this->nrOrder=$this->nrOrder+1;
//        // }else{
//        //     $this->nrOrder=1;
//        // }
//        // if( $this->clientNrOrder > 0){
//        //     $this->clientNrOrder=$this->clientNrOrder+1;
//        // }else{
//        //     $this->clientNrOrder=1;
//        // }
//    }
//
    // /**
    //  * Set clientNrOrderOpt
    //  *
    //  * @param string $clientNrOrderOpt
    //  *
    //  * @return Clients
    //  */
    // public function setClientNrOrderOpt($clientNrOrderOpt)
    // {
    //     $this->clientNrOrderOpt = $clientNrOrderOpt;

    //     return $this;
    // }

    // /**
    //  * Get clientNrOrderOpt
    //  *
    //  * @return string
    //  */
    // public function getClientNrOrderOpt()
    // {
    //     return $this->clientNrOrderOpt;
    // }

    // public static function getFields($type = null)
    // {
    //     switch ($type) {
    //         case 'orders' :
    //             $fields = ['id', 'code', 'name', 'street', 'zipCode', 'city', 'tel', 'mobile'];
    //             break;
    //         case 'dic' :
    //             $fields = ['id', 'code', 'name', 'nip'];
    //             break;
    //         default :
    //             $fields = parent::getFields($type);
    //     }
    //     return $fields;
    // }

//}
    // /**
	// 	* @var string
	// 	*/
    //     private $limits;
        
        
    //     /**
    //         * Set limits
    //         *
    //         * @param string $limits
    //         *
    //         * @return Clients
    //         */
    //     public function setLimits($limits){
    //         $this->limits = $limits;
            
    //         return $this;
    //     }
        
    //     /**
    //         * Get limits
    //         *
    //         * @return string
    //         */
    //     public function getLimits(){
    //         return $this->limits;
    //     }
    //     /**
    //         * @var string
    //         */
    //     private $clientNrOrderOpt;
    
// /**
//  * @var string
//  */
// private $nrOrderGenerator;

// /**
//  * @var integer
//  */
// private $clientNrOrder = 0;

// /**
//  * @var string
//  */
// private $clientNrOrderGenerator;


// /**
//  * @var integer
//  */
// private $nrOrder;

// /**
//  * @var integer
//  */
// private $minWidth;

// /**
//  * @var integer
//  */
// private $calcWidth;

// /**
//  * @var string
//  */
// private $faktId;

    // /**
	// 	* Set nrOrder
	// 	*
	// 	* @param integer $nrOrder
	// 	*
	// 	* @return Clients
	// 	*/
    //     public function setNrOrder($nrOrder){
    //         $this->nrOrder = $nrOrder;
    
    //         return $this;
    //     }
        
    //     /**
    //         * Get nrOrder
    //         *
    //         * @return integer
    //         */
    //     public function getNrOrder(){
    //          return $this->nrOrder;
    //     }
        
    //     /**
    //         * Set minWidth
    //         *
    //         * @param integer $minWidth
    //         *
    //         * @return Clients
    //         */
    //     public function setMinWidth($minWidth){
    //         $this->minWidth = $minWidth;
    
    //         return $this;
    //     }
    
    //     /**
    //         * Get minWidth
    //         *
    //         * @return integer
    //         */
    //     public function getMinWidth(){
    //         return $this->minWidth;
    //     }
        
    //     /**
    //         * Set calcWidth
    //         *
    //         * @param integer $calcWidth
    //         *
    //         * @return Clients
    //         */
    //     public function setCalcWidth($calcWidth){
    //         $this->calcWidth = $calcWidth;
    
    //         return $this;
    //     }
        
    //     /**
    //         * Get calcWidth
    //         *
    //         * @return integer
    //         */
    //     public function getCalcWidth(){
    //         return $this->calcWidth;
    //     }
    
    //     /**
    //         * Set faktId
    //         *
    //         * @param string $faktId
    //         *
    //         * @return Clients
    //         */
    //     public function setFaktId($faktId){
    //          $this->faktId = $faktId;
             
    //          return $this;
    //         }
            
    //         /**
    //         * Get faktId
    //         *
    //         * @return string
    //         */
    //     public function getFaktId(){
    //         return $this->faktId;
    //     }
    //     /**
	// 	* Set nrOrderGenerator
	// 	*
	// 	* @param string $nrOrderGenerator
	// 	*
	// 	* @return Clients
	// 	*/
	// public function setNrOrderGenerator($nrOrderGenerator){
    //     $this->nrOrderGenerator = $nrOrderGenerator;
        
    //     return $this;
    // }
    
    // /**
	// 	* Get nrOrderGenerator
	// 	*
	// 	* @return string
	// 	*/
	// public function getNrOrderGenerator(){
    //     return $this->nrOrderGenerator;
    // }
    
    // /**
	// 	* Set clientNrOrder
	// 	*
	// 	* @param integer $clientNrOrder
	// 	*
	// 	* @return Clients
	// 	*/
	// public function setClientNrOrder($clientNrOrder){
    //      $this->clientNrOrder = $clientNrOrder;

    //      return $this;
    // }
    
    // /**
	// 	* Get clientNrOrder
	// 	*
	// 	* @return integer
	// 	*/
	// public function getClientNrOrder(){
    //     return $this->clientNrOrder;
    // }

    // /**
	// 	* Set clientNrOrderGenerator
	// 	*
	// 	* @param string $clientNrOrderGenerator
	// 	*
	// 	* @return Clients
	// 	*/
	// public function setClientNrOrderGenerator($clientNrOrderGenerator){
    //     $this->clientNrOrderGenerator = $clientNrOrderGenerator;

    //     return $this;
    // }
    
    // /**
	// 	* Get clientNrOrderGenerator
	// 	*
	// 	* @return string
	// 	*/
	// public function getClientNrOrderGenerator(){
    //     return $this->clientNrOrderGenerator;






}
