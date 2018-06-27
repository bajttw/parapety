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

    protected function newCustomEntity()
    {
        $this->entity->setSequence($this->getEntityHelper()->getCount()+1);
        return $this->entity;
    }
 
    protected function preUpdateAction(Request $request, $id, $cid = 0){
        $this->entity->saveFieldValue('active');
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
