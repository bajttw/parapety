<?php

namespace AppBundle\Entity;

/**
    * Colors
    */
class Colors extends ParametersWithUpload
{
    const en='colors';
    const ec='Colors';  
    const idPrototype = '__cid__';
    
 // <editor-fold defaultstate="collapsed" desc="Fields functions"> 

    /**
		* Set active
		*
		* @param boolean $active
		*
		* @return Colors
		*/
    public function setActive(bool $active)
    {
        if($this->active != $active){
            $this->saveFieldValue('active');
        }
        return parent::setActive($active);
    }

 // </editor-fold> 

}
