<?php

namespace AppBundle\Controller;

use AppBundle\Controller\AppController;

/**
 * Trims controller.
 *
 */
class TrimsController extends AppController
{
    const en='trims';
    const ec='Trims';

 //  <editor-fold defaultstate="collapsed" desc="Custom functions">

    protected function newCustomEntity()
    {
        $this->entity->setSequence($this->getEntityHelper()->getCount()+1);
        return $this->entity;
    }

 // </editor-fold>   

}
