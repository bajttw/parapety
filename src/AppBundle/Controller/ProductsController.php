<?php

namespace AppBundle\Controller;

use AppBundle\Controller\AppController;
use AppBundle\Utils\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Productions controller.
 *
 */
class ProductsController extends AppController
{
    const en = 'products';
    const ec = 'Products';

    // public static function getFilters($type = 'index', $options = [])
    // {
    //     $id = Utils::deep_array_value('id', $options);
    //     $pid = Utils::deep_array_value('pid', $options);
    //     $did = Utils::deep_array_value('did', $options);
    //     $filters = [];
    //     $fs = [
    //         "cutTime" => [
    //             'name' => 'cutTime',
    //             'data' => [
    //                 ['v' => 0, 'n' => 'przed'],
    //                 ['v' => 1, 'n' => 'po'],
    //             ],
    //             'd' => [
    //                 'widget' => 'multiselect',
    //                 'filter-options' => json_encode(['type' => 'set']),
    //             ],
    //         ],
    //         "packingTime" => [
    //             'name' => 'packingTime',
    //             'data' => [
    //                 ['v' => 0, 'n' => 'przed'],
    //                 ['v' => 1, 'n' => 'po'],
    //             ],
    //             'd' => [
    //                 'widget' => 'multiselect',
    //                 'filter-options' => json_encode(['type' => 'set']),
    //             ],
    //         ],
    //         "hiddenProduction" => [
    //             "name" => 'production',
    //             'type' => 'hidden',
    //             'value' => $pid,
    //         ],
    //         "hiddenDelivery" => [
    //             "name" => 'delivery',
    //             'type' => 'hidden',
    //             'value' => $did,
    //         ]

    //     ];
    //     switch ($type) {
    //         case 'index':
    //         case 'productions_form':
    //             foreach (['cutTime', 'packingTime'] as $f) {
    //                 self::addFilter($filters, $fs[$f], $f);
    //             }
    //             break;

    //         case 'productions_table':
    //             foreach (['hiddenProduction'] as $f) {
    //                 self::addFilter($filters, $fs[$f], $f);
    //             }
    //             break;
    //         case 'deliveries_table':
    //             foreach (['hiddenDelivery'] as $f) {
    //                 self::addFilter($filters, $fs[$f], $f);
    //             }
    //             break;
    //     }
    //     return $filters;
    // }

    public static function getActions($type = 'view', $options = [])
    {
        $actions = [];
        $all = [
            'edit' => ['action' => 'edit', 'type' => 'm', 'target' => 'products'],
            'show' => ['action' => 'show', 'browserAction' => true],
            'delete' => ['action' => 'delete', 'type' => 'm', 'target' => 'products'],
        ];
        switch ($type) {
            case 'index':
                $as = ['show', 'edit'];
                break;
            case 'view':
            default:
                $as = ['show', 'edit'];
        }
        foreach ($as as $a) {
            $actions[$a] = $all[$a];
        }
        return $actions;
    }

    public static function getToolbarBtn($type = 'index', $options = [])
    {
        $b = [
            'new' => [
                'action' => 'new',
                'attr' => [
                    'class' => 'btn-primary',
                    'target' => '_blank',
                ],
                'routeParam' => ['type' => 'w'],
            ],
        ];
        $btns = [];
        switch ($type) {
            case 'index':
                foreach (['new'] as $n) {
                    $btns[] = $b[$n];
                }
                break;
            default:
                foreach (['new'] as $n) {
                    $btns[] = $b[$n];
                }
        }
        return $btns;

    }

//  <editor-fold defaultstate="collapsed" desc="Custom functions">
    protected function customMessages(&$messages, $type)
    {

        switch ($type) {
            case 'create':
            case 'update':
                break;

        }
        return $messages;
    }

// </editor-fold>

//  <editor-fold defaultstate="collapsed" desc="Actions">

// </editor-fold>

}
