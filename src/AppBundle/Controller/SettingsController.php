<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Utils\Utils;

class SettingsController extends AppController
{
    const en='settings';
    const ec='Settings';

    public $ownTemplate=['index'];

    // public static function getFilters($type='index', $options=[]){
    //     $cid = Utils::deep_array_value('cid', $options);
    //     $isClient=$cid != null;
    //     $filters=[];
    //     $fs = [
    //         'client' => [
    //             'name' => 'client',
    //             'source' => [
    //                 'type' => 'entity',
    //                 'query' => 'Clients',
    //             ],
    //             'add' => [
    //                 'start' => [
    //                     ['v' => 'null', 'n' => 'globalnie']
    //                 ]
    //             ],
    //             'attr' => [
    //                 'multiple' => 'multiple'
    //             ],
    //             'd' => [
    //                 'widget' => 'multiselect',
    //                 'def-value' => ['null']               
    //             ]
    //         ]
    //     ];
    //     switch($type){
    //         case 'index' :
    //             foreach (['client'] as $f) {
    //                 self::addFilter($filters, $fs[$f], $f);
    //             }
    //         break;
    //         case 'table_client':
    //             self::addFilter($filters, self::genFilter('client_hidden', $options), 'client');
    //         break;
    //         default:

    //     }
    //     return $filters;
    // }

    // public static function getToolbarBtn($type='index', $options=[] )
    // {
    //     $cid = Utils::deep_array_value('cid', $options);
    //     $b=[
    //         'new' => [
    //             'action' => 'new',
    //             'isClient' => $cid ? true : false,
    //             'modal' => static::en,
    //             'attr' => ['class' => 'btn-primary'],
    //             'routeParam' => $cid ? [ 'cid' => $cid ] : []
    //         ]
    //     ];
    //     $btns=[];
    //     switch($type){
    //         default:
    //             foreach(['new'] as $n){
    //                 $btns[]=$b[$n];
    //             }
    //     }
    //     return $btns;
    // }
    
    public function clientAjaxDataAction(Request $request, $id, $cid = 0)
    {
        if (!$this->preAction($request, $cid, ['entitySettings' => false])) {
            return $this->responseAccessDenied(true);
        }
        $this->getEntityFromBase($id);
        return new JsonResponse(['entity' => $this->entity->getShowData()]);
    }




    public function clientNewAction(Request $request, $cid = 0)
    {
        if (!$this->preAction($request, $cid)) {
            return $this->responseAccessDenied();
        }
        $this->setTemplate('edit');
        $this->newEntity();
        $this->entity->setClient($this->client);
        $this->createCreateForm();
        return $this->renderSystem(true);
    }



}
