<?php

namespace AppBundle\Entity;

/**
		* Sizes
		*/
class Sizes extends Parameters
{
    const en='sizes';
    const ec='Sizes';  
    const idPrototype = '__sid__';
 
 // <editor-fold defaultstate="collapsed" desc="Fields functions"> 

    /**
		* Set active
		*
		* @param boolean $active
		*
		* @return Sizes
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
