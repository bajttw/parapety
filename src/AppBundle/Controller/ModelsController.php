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

    protected function newEntity(array $options = [] ):void
    {
        $options['defaults']['sequence']=$this->getEntityHelper()->getCount(static::ec)+1;
        parent::newEntity($options);
    }
    
 // </editor-fold>   

}
