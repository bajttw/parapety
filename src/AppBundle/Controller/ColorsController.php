<?php

namespace AppBundle\Controller;

use AppBundle\Controller\AppController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Colors controller.
 *
 */
class ColorsController extends AppController
{
    const en='colors';
    const ec='Colors';
 //  <editor-fold defaultstate="collapsed" desc="Custom functions">

    protected function newEntity(array $options = [] ):void
    {
        $options['defaults']['sequence']=$this->getEntityHelper()->getCount(static::ec)+1;
        parent::newEntity($options);
    }
 

    protected function postUpdateAction(&$dataReturn){
        if($this->entity->checkFieldChange('active') === true){
            $iController=self::getEntityController('PriceListItems');
            $iController::updateActive($this->getEntityManager(), null, $this->entity );
        }
        return $dataReturn;
    }

 // </editor-fold>   
  
}
