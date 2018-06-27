<?php

namespace AppBundle\Controller;

use AppBundle\Controller\AppController;

/**
 * Models controller.
 *
 */
class ModelsController extends AppController
{
    const en='models';
    const ec='Models';

 //  <editor-fold defaultstate="collapsed" desc="Custom functions">

    protected function newCustomEntity()
    {
        $this->entity->setSequence($this->getEntityHelper()->getCount()+1);
        return $this->entity;
    }
    
 // </editor-fold>   

}
