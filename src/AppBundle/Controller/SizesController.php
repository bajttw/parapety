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

    protected function newEntity(array $options = [] ):void
    {
        $options['defaults']['sequence']=$this->getEntityHelper()->getCount(static::ec)+1;
        parent::newEntity($options);
    }

 // </editor-fold>   
   
}
