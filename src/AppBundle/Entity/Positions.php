<?php

namespace AppBundle\Entity;
use AppBundle\Utils\Utils;
use Doctrine\Common\Collections\ArrayCollection;
/**
			* Positions
			*/
class Positions extends AppEntity{
	const en='positions';
	const ec='Positions';
    const idPrototype = '__pid__';

//  <editor-fold defaultstate="collapsed" desc="Fields">    
    public static $shortNames=[
		'id' => 'id',
		'nr' => 'nr',
		'width' => 'w',
		'length' => 'l',
		'widthcm' => 'wcm',
		'lengthcm' => 'lcm',
		'model' => 'm',
		'size' => 's',
		'color' => 'cl',
        'trims' => 'ts',
		'quantity' => 'q',
		'area' => 'a',
		'value' => 'v',
		'progress' => 'p',
        'clientComment' => 'c',
		'positionComment' => 'pc',
		'prodComment' => 'dc',
		'clientInfo' => 'ci',
		'uploads' => 'u',
		'childs' => [
			'products' => 'Products',
			'order' => 'Orders',
			'model' => 'Models',
			'size' => 'Sizes',
			'color' => 'Colors',
			'trims' => 'Trims',
			'uploads' => 'Uploads'
		]
	];
	
	public static function getFields($type = null)
    {
        switch ($type) {
            case 'productions_orders':
                $fields = array_merge(parent::getFields($type), ['products'] );
            break;
            default:
                $fields = parent::getFields($type);
        }
        return $fields;
    }

// </editor-fold>       
    
//  <editor-fold defaultstate="collapsed" desc="Utils">    
    public function mmCalcArea(){
        if(!is_array($this->mmArea)){
            $area=$this->length * $this->width * $this->quantity ;
            $this->mmArea= $area;
        }
        return $this->mmArea;
    }

    public function calcArea(){
        $this->area= $this->mmCalcArea()/1000000;
    }
// </editor-fold>       


//  <editor-fold defaultstate="collapsed" desc="Variables">    
	/**
		* @var integer
		*/
	private $id;

	/**
		* @var integer
		*/
	private $nr = 1;

	/**
		* @var integer
		*/
	private $width = 0;

	/**
		* @var integer
		*/
	private $length = 0;

	/**
		* @var float
		*/
	private $widthcm = 0;

	/**
		* @var float
		*/
	private $lengthcm = 0;

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
	private $progress = 1;

    /**
     * @var string
     */
	private $trims;
     
    /**
		* @var string
		*/
	private $clientComment;

	/**
		* @var string
		*/
	private $positionComment;

	/**
		* @var string
		*/
	private $prodComment;

	/**
		* @var string
		*/
	private $clientInfo;

	/**
		* @var \Doctrine\Common\Collections\Collection
		*/
	private $products;
		
	/**
		* @var \AppBundle\Entity\Orders
		*/
	private $order;
		
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
		* @var \Doctrine\Common\Collections\Collection
		*/
    private $uploads;

    private $mmArea=null;    
    
// </editor-fold>       
    
		
	/**
		* Constructor
		*/
	public function __construct($options=[]){
		parent::__construct($options);
		$this->products = new \Doctrine\Common\Collections\ArrayCollection();
		$this->uploads = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
		* Get id
		*
		* @return integer
		*/
	public function getId(){
		return $this->id;
	}

	/**
		* Set nr
		*
		* @param integer $nr
		*
		* @return Positions
		*/
	public function setNr($nr){
		$this->nr = $nr;

		return $this;
	}

	/**
		* Get nr
		*
		* @return integer
		*/
	public function getNr(){
		return $this->nr;
	}

	/**
		* Set width
		*
		* @param float $width
		*
		* @return Positions
		*/
	public function setWidth($width){
		$this->width = $width;
		$this->widthcm = $width/10;
		return $this;
	}

	/**
		* Get width
		*
		* @return float
		*/
	public function getWidth(){
		return $this->width;
	}

	/**
		* Set length
		*
		* @param float $length
		*
		* @return Positions
		*/
	public function setLength($length){
		$this->length = $length;
		$this->lengthcm = $length/10;
		return $this;
	}

	/**
		* Get length
		*
		* @return float
		*/
	public function getLength(){
		return $this->length;
	}

	/**
		* Set widthcm
		*
		* @param float $widthcm
		*
		* @return Positions
		*/
	public function setWidthcm($widthcm){
		$this->widthcm = $widthcm;
		$this->width = intval(10*$widthcm);

		return $this;
	}

	/**
		* Get widthcm
		*
		* @return float
		*/
	public function getWidthcm(){
		$this->widthcm = $this->width/10;
		return $this->widthcm;
	}

	/**
		* Set lengthcm
		*
		* @param float $lengthcm
		*
		* @return Positions
		*/
	public function setLengthcm($lengthcm){
		$this->lengthcm = $lengthcm;
		$this->length = intval(10*$lengthcm);

		return $this;
	}

	/**
		* Get lengthcm
		*
		* @return float
		*/
	public function getLengthcm(){
		$this->lengthcm = $this->length/10;
		return $this->lengthcm;
	}

	/**
		* Set quantity
		*
		* @param integer $quantity
		*
		* @return Positions
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
		* @return Positions
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
		* Set value
		*
		* @param float $value
		*
		* @return Positions
		*/
	public function setValue($value){
		$this->value = $value;

		return $this;
	}

	/**
		* Get value
		*
		* @return float
		*/
	public function getValue(){
		return $this->value;
	}

	/**
		* Set progress
		*
		* @param integer $progress
		*
		* @return Positions
		*/
	public function setProgress($progress){
		$this->progress = $progress;

		return $this;
	}

	/**
		* Get progress
		*
		* @return integer
		*/
	public function getProgress(){
		return $this->progress;
	}

    public function calcProgress(){
        $this->progress=0;
        // if($this->package){
        //     $this->progress=20;
        // }
        return $this->progress;
    }

    
    /**
        * Set trims
        *
        * @param string $trims
        *
        * @return Positions
        */
    public function setTrims($trims)
    {
        $this->trims = $trims;

        return $this;
    }
 
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
		* Set clientComment
		*
		* @param string $clientComment
		*
		* @return Positions
		*/
	public function setClientComment($clientComment){
		$this->clientComment = $clientComment;

		return $this;
	}

	/**
		* Get clientComment
		*
		* @return string
		*/
	public function getClientComment(){
		return $this->clientComment;
	}

	/**
		* Set positionComment
		*
		* @param string $positionComment
		*
		* @return Positions
		*/
	public function setPositionComment($positionComment){
		$this->positionComment = $positionComment;

		return $this;
	}

	/**
		* Get positionComment
		*
		* @return string
		*/
	public function getPositionComment(){
		return $this->positionComment;
	}

	/**
		* Set prodComment
		*
		* @param string $prodComment
		*
		* @return Positions
		*/
	public function setProdComment($prodComment){
		$this->prodComment = $prodComment;

		return $this;
	}

	/**
		* Get prodComment
		*
		* @return string
		*/
	public function getProdComment(){
		return $this->prodComment;
	}

	/**
		* Set clientInfo
		*
		* @param string $clientInfo
		*
		* @return Positions
		*/
	public function setClientInfo($clientInfo){
		$this->clientInfo = is_array($clientInfo) ?  json_encode($clientInfo) : $clientInfo;
		return $this;
	}

	/**
		* Get clientInfo
		*
		* @return string
		*/
	public function getClientInfo($decode=false){
		
		return $decode && Utils::is_JSON_string($this->clientInfo) ? json_decode($this->clientInfo, true) : $this->clientInfo;
	}

	/**
		* Add product
		*
		* @param \AppBundle\Entity\Products $product
		*
		* @return Positions
		*/
	public function addProduct(\AppBundle\Entity\Products $product){
		$product->setPosition($this);
		$this->products[] = $product;

		return $this;
	}

	/**
		* Remove product
		*
		* @param \AppBundle\Entity\Products $product
		*/
	public function removeProduct(\AppBundle\Entity\Products $product){
		$this->products->removeElement($product);
	}

	public function removeProducts(){
		foreach($this->products as $product){
			$this->controller->getEntityManager()->remove($product);
			$this->removeProduct($product);
		}
		return $this;
	}
	/**
		* Get products
		*
		* @return \Doctrine\Common\Collections\Collection
		*/
	public function getProducts(){
		return $this->products;
	}

	/**
		* Set order
		*
		* @param \AppBundle\Entity\Orders $order
		*
		* @return Positions
		*/
	public function setOrder(\AppBundle\Entity\Orders $order = null){
		$this->order = $order;

		return $this;
	}

	/**
		* Get order
		*
		* @return \AppBundle\Entity\Orders
		*/
	public function getOrder(){
		return $this->order;
	}
	
	/**
		* Set model
		*
		* @param \AppBundle\Entity\Models $model
		*
		* @return Positions
		*/
	public function setModel(\AppBundle\Entity\Models $model = null){
		$this->model = $model;

		return $this;
	}

	/**
		* Get model
		*
		* @return \AppBundle\Entity\Models
		*/
	public function getModel(){
		return $this->model;
	}

	/**
		* Set size
		*
		* @param \AppBundle\Entity\Sizes $size
		*
		* @return Positions
		*/
	public function setSize(\AppBundle\Entity\Sizes $size = null){
		$this->size = $size;

		return $this;
	}

	/**
		* Get size
		*
		* @return \AppBundle\Entity\Sizes
		*/
	public function getSize(){
		return $this->size;
	}

	/**
		* Set color
		*
		* @param \AppBundle\Entity\Colors $color
		*
		* @return Positions
		*/
	public function setColor(\AppBundle\Entity\Colors $color = null){
		$this->color = $color;

		return $this;
	}

	/**
		* Get color
		*
		* @return \AppBundle\Entity\Colors
		*/
	public function getColor(){
		return $this->color;
	}

	/**
		* Add upload
		*
		* @param \AppBundle\Entity\Uploads $upload
		*
		* @return Positions
		*/
	public function addUpload(\AppBundle\Entity\Uploads $upload){
        $this->uploads->add($uploads);
		return $this;
	}

	/**
		* Remove upload
		*
		* @param \AppBundle\Entity\Uploads $upload
		*/
	public function removeUpload(\AppBundle\Entity\Uploads $upload){
		$this->uploads->removeElement($upload);
	}

	/**
		* Get uploads
		*
		* @return \Doctrine\Common\Collections\Collection
		*/
	public function getUploads(){
		return $this->uploads;
	}


    // public function checkUpdates(){
		
	// 	$this->updateUploads();
    // }
    
	/**
		* @ORM\PrePersist
		*/
	public function prePersistPosition(){
        $this->calcArea();
	}

	/**
		* @ORM\PreUpdate
		*/
	public function preUpdatePosition(){
        $this->calcArea();
	}

	public function validate($options=[] ){
		$validate=parent::validate($options);

		return $validate;
	}

    public function importMedos($item, $options){
		$this->setLength(Utils::deep_array_value('_size_a', $item, 0));
        $this->setWidth(Utils::deep_array_value('_size_b', $item, 0));
        $this->setQuantity(Utils::deep_array_value('quantity', $item, 0));
		$this->setClientComment(Utils::deep_array_value('sillDescription', $item));
		$this->setPositionComment(Utils::deep_array_value('_comments', $item));
		$imp=$this->validate($options);
		foreach ($options['map_params'] as $n => $mn) {
			$imp_par=Utils::deep_array_value($mn, $item, '');
			if(array_key_exists($imp_par, $options[$n])){
				$set_fn='set'.ucfirst($n);
				$this->$set_fn($this->controller->getEntityManager()->getReference(self::getChildEntityClass($n), $options[$n][$imp_par]));
			}else{
				$imp['status']='error';
				$imp['messages'][]=$this->genMessage('wrong_value', $n).' '.$this->genMessage('correct_values', $n).' '.join(', ', array_keys($options[$n]));
			}
		}
		$components=Utils::deep_array_value('components', $item);
        if(is_array($components)){
			$trims=[];
			$imp_trims=[];
			foreach($components as $c){
				$c_val=Utils::deep_array_value('_name', $c);
				array_push($imp_trims, $c_val );
				if(array_key_exists($c_val, $options['trims'])){
					array_push($trims, $c_val);
				}
            }
            if(count($trims)>0){
                $this->setTrims(implode(',', $trims));
            }
            if(count($trims) < count($components) ){
                if($imp['status'] != 'error' ){
                    $imp['status'] ='warning';
                }
				$imp['messages'][]=$this->genMessage('import_trims', null, [$this->getTrims(), join(', ', $imp_trims )]);
            }
        }
		$labels=[];
        foreach(Utils::deep_array_value('labels', $item, []) as $label){
            array_push($labels, $label);
		}
		$purchaseItemMedosNumber = Utils::deep_array_value("_purchaseItemMedosNumber", $item, '');
        $this->setClientInfo([
			"id" => $purchaseItemMedosNumber,
            '_name' => Utils::deep_array_value('_name', $item, ''),
            'symbol' => Utils::deep_array_value('symbol', $item, ''),
            // 'componentAttachment' =>  urlencode(Utils::deep_array_value( 'componentAttachment', $item, '')),
            'componentAttachment' => Utils::deep_array_value( 'componentAttachment', $item, ''),
            'labels' => $labels
        ]);
		$imp['_purchaseItemMedosNumber']=$purchaseItemMedosNumber;
        return $imp;
    }

    public function getDescription()
    {
        $tab=[
            "FO" => "F",
            "MD" => "M",
            "BS" => "B",
            "ST" => ""
        ];
		return $this->getColor()->getSymbol()
			.' '.$this->getWidthcm()
			.'/'.$this->getLengthcm()
			.' '.$tab[$this->getModel()->getSymbol()];
    }

}
