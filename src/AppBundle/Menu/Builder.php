<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface{
    use ContainerAwareTrait;
    public function clientMenu(FactoryInterface $factory, array $options){
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');
        $menu->addChild('clients.label.account', array())
            ->setAttribute('dropdown', true);
        $menu['clients.label.account']->addChild('clients.label.myAccount', array(
            'route' => 'client_clients_edit',
            'routeParameters' => array('cid' => $options['clientid'], 'id' => $options['clientid'] ),
            'linkAttributes' => array("data-target" => "#myModal", "data-toggle" => "modal"),        
            ));
        $menu['clients.label.account']->addChild('clients.label.users', array(
            'route' => 'client_users',
            'routeParameters' => array('cid' => $options['clientid']),
            ));
            //        $menu->addChild('Zamówienia', array(
            //            'route' => 'client_orders',
            //            'routeParameters' => array('cid' => $options['clientid'])
            //            ));

        return $menu;
    }
    public function employeeMenu(FactoryInterface $factory, array $options){
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');
        // $menu->addChild('clients.label.clients', [  'route' => 'clients']);
        $menu->addChild('clients.label.clients', [
            'route' => 'clients',
            'attributes' => [
                    'dropdown' => true,
            ]
        ]);
        $menu['clients.label.clients']->addChild('clients.label.list', array('route' => 'clients'));
        $menu['clients.label.clients']->addChild('clients.label.service', array('route' => 'clients_service'));        
        $menu->addChild('orders.label.index', ['route' => 'orders'] );
        $menu->addChild('productions.label.index', ['route' => 'productions'] );
        $menu->addChild('settings.label.settings')
                ->setAttribute('dropdown', true);
        //        $menu['Ustawienia']->addChild('Parametry', array(
        //            'route' => 'parameters_edit',
        //            'routeParameters' => array('id' => 1),
        //            'linkAttributes' => array("data-target" => "#myModal", "data-toggle" => "modal"),        
        //        ));
        //        $menu['settings.label.settings']->addChild('usergroups.label.usergroups', array('route' => 'usergroups'));
        //        $menu['settings.label.settings']->addChild('pricelists.label.pricelists', array('route' => 'pricelists'));
        //        $menu['settings.label.settings']->addChild('servicecatalog.label.servicecatalog', array('route' => 'servicecatalog'));
        //        $menu['settings.label.settings']->addChild('prices.label.prices', array('route' => 'serwis_prices'));
        return $menu;
    }

    public function adminMenu(FactoryInterface $factory, array $options){
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');
        $menu->addChild('clients.label.clients')
            ->setAttribute('dropdown', true);
        $menu['clients.label.clients']->addChild('clients.label.list', array('route' => 'app_admin_clients'));
        $menu['clients.label.clients']->addChild('clients.label.service', array('route' => 'app_admin_clients_service'));        
        $menu->addChild('orders.label.index', ['route' => 'app_admin_orders'] );
        $menu->addChild('productions.label.index', ['route' => 'app_admin_productions'] );
        $menu->addChild('products.label.index', ['route' => 'app_admin_products'] );
        $menu->addChild('deliveries.label.index', ['route' => 'app_admin_deliveries'] );
        $menu->addChild('invoices.label.index', ['route' => 'app_admin_invoices'] );
        $menu->addChild('settings.label.settings')
                ->setAttribute('dropdown', true);
        $menu['settings.label.settings']->addChild('models.label.index', array('route' => 'app_admin_models'));
        $menu['settings.label.settings']->addChild('trims.label.index', array('route' => 'app_admin_trims'));
        $menu['settings.label.settings']->addChild('colors.label.index', array('route' => 'app_admin_colors'));
        $menu['settings.label.settings']->addChild('sizes.label.index', array('route' => 'app_admin_sizes'));
        $menu['settings.label.settings']->addChild('users.label.index', array('route' => 'app_admin_users'));
        $menu['settings.label.settings']->addChild('usersgroups.label.index', array('route' => 'app_admin_usersgroups'));
        $menu['settings.label.settings']->addChild('clientsgroups.label.index', array('route' => 'app_admin_clientsgroups'));   
        $menu['settings.label.settings']->addChild('settings.label.index', array('route' => 'app_admin_settings'));
        $menu['settings.label.settings']->addChild('pricelistitems.label.index', array('route' => 'app_admin_pricelistitems'));
        $menu['settings.label.settings']->addChild('pricelists.label.index', array('route' => 'app_admin_pricelists'));
        return $menu;
    }
}