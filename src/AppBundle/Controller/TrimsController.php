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

    protected function newEntity(array $options = [] ):void
    {
        $options['defaults']['sequence']=$this->getEntityHelper()->getCount(static::ec)+1;
        parent::newEntity($options);
    }

 // </editor-fold>   

}
