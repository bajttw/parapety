<?php

namespace AppBundle\Controller;

use AppBundle\Controller\AppController;

/**
 * Sizes controller.
 *
 */
class SizesController extends AppController
{
    const en='sizes';
    const ec='Sizes';

 //  <editor-fold defaultstate="collapsed" desc="Custom functions">

    protected function newCustomEntity()
    {
        $this->entity->setSequence($this->getEntityHelper()->getCount()+1);
        return $this->entity;
    }

 // </editor-fold>   
   
}
