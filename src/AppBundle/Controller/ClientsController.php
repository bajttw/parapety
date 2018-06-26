<?php

namespace AppBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use AppBundle\Utils\Utils;

class ClientsController extends AppController{
    const en='clients';
    const ec='Clients';

    // public static function getFilters($type='index', $options=[]){
    //     $id=array_key_exists('id', $options) ? $options['id'] : null;
    //     $filters=[
    //     ];
    //     $fs=[
    //         'active' => 'active',
    //         'users' =>[
    //             'name' => 'users.id',
    //             'source' => [
    //                 'type' => 'entity',
    //                 'query' => 'Users',
    //                 'options' => [
    //                     'filters' => [
    //                         'type' => [
    //                             'condition' => 'gte',
    //                             'value' => '2'
    //                         ],
    //                         'enabled' => [ 'value' => true]
    //                     ]
                        
    //                 ]
    //             ],
    //             'attr' => [
    //                 'multiple' => 'multiple'
    //             ],
    //             'd' => [
    //                 'widget' => 'multiselect'                
    //             ]
    //         ],
    //         'regular' =>[
    //             'name' => 'regular',
    //             'data' => [
    //                 ['v' => '1', 'n' => 'stały'],
    //                 ['v' => '0', 'n' => 'zwykły']
    //                 ],
    //                 'd' => [
    //                     'widget' => 'multiselect'
    //                 ],
    //                 'attr' => [
    //                     'multiple' => 'multiple'
    //         ]
    //         ]
    //     ];
    //     switch($type){
    //         case 'index':
    //         case 'service':
    //         default:
    //             foreach(['active', 'regular'] as $f){
    //                 self::addFilter($filters, $fs[$f], $f);
    //             }
    //         break;
    //     }
    //     return $filters;
    // }

    public static function getActions($type = 'index', $options=[]){
        $actions= [
            'edit' => [ 'action' => 'edit', 'type' => 'm', 'target' => self::en],
            'delete' => [ 'action' => 'delete', 'type' => 'm', 'target' => self::en]
        ];
        return $actions;
    }

    public static function getToolbarBtn($type='index', $options=[] ){
        return [
            [   
                'action' => 'new', 
                'modal' => self::en, 
                'attr' => [ 'class' => 'btn-primary' ]
            ]
        ];
    }

    public static function getModal(){
        return [
            "dialog_attr" => [
                'class' => 'modal-xl'
            ]
        ];
    }
   
//  <editor-fold defaultstate="collapsed" desc="Custom functions">
    
    protected function customMessages(&$messages, $type){

        switch ($type){
            case 'create':
            case 'update':
                $messages['message'].=" <i>".$this->entity->getName()."</i>";
            break;        
                
        }
        return $messages;
    }

    protected function customCreateAction(&$dataReturn){
        $dataReturn=['show' => 1];
        $email=$this->entity->getEmail();
        if ($email){
            $userManager = $this->get('fos_user.user_manager');
            $user=$userManager->findUserByEmail($email);
            if ($userManager->findUserByEmail($email) == null){
                $defaultPass=$this->getSettingsHelper()->getSettingValue('password-default');
                //            $this->getEntityManager()->persist($this->entity);
                $user= $userManager->createUser();
                $user->addClient($this->entity);
                $user->setUsername($this->entity->getEmail());
                $user->setEmail($this->entity->getEmail());
            $user->setRoles(['ROLE_USER']);
            $user->setPlainPassword($defaultPass);
            try {
                $userManager->updateUser($user, true);
                    $dataReturn['messages']['childs']=[
                        $this->responseMessage([
                            'title' => $this->trans($this->getTransHelper()->titleText('user_created', 'Clients')),
                            'message' => [
                                $this->trans($this->messageText('login', 'Users'), [$user->getUsername()]),
                                $this->trans($this->messageText('password', 'Users'), [$defaultPass])

                            ]
                        ], null, false)
                    ];
                }catch(\Exception $e) {
                    $dataReturn['errors']['childs']=[ $this->errorMessage([
                        'title' => $this->trans($this->getTransHelper()->titleText('error.user_create')),
                        'message' => $e->getMessage()
                        ], null, false)
                    ];
                }
            }else{
                $error= new FormError($this->trans($this->messageText('email_exist', 'Users')));        
                $this->formSystem->get('email')->addError($error);
                $dataReturn['errors']['childs']= [ $this->errorMessage([
                    'title' => 'error.user_create',
                    'message' => 'email_exist'
                ])];
            }
        }
        return $dataReturn;
    }

    protected function setCustomFormOptions(){
        $this->addModalsField([
            [
                'name' => 'comment',
                'fieldtype' => 'textarea'
            ]
        ]);
        return $this;
    }


// </editor-fold>   

    private function genClientServicePanel(string $entityClassName, string $clientIdPrototype,  $active=false ):array
    {
        return $this->genPanel($entityClassName, [
            'active' => $active,
            'content' => $this->tmplPath('index', '', 'Panel'),
            'toolbars' => [
                $this->genToolbar( 'service', $entityClassName, [ 'tmpl' => true, "cid" => $clientIdPrototype ]),
                $this->getFilterbarGenerator()->generate( 'service', $entityClassName)
            ],
            'table' => $this->getDTGenerator()->generate('panel', $entityClassName, [
                'actions' => true,
                'export' => true,
                'clientId' => $clientIdPrototype,
                'ajax' =>[
                    'filtersType' => 'table_service'
                ]
                // 'd' => [
                //     'ajax' => [
                //         'url' => $this->getRouteHelper()->getClientUrl('ajax', $entityClassName, ['cid' => $clientIdPrototype ])
                //     ],
                //     'filters' => $this->getFilterHelper()->generate('table_service', $entityClassName, [ 'values' => ['client' => $clientIdPrototype ]]) 
                // ]
            ])
        ]);
    }
    
// <editor-fold defaultstate="collapsed" desc="Actions">  
    
    public function serviceAction(Request $request, $uid=0){
        if (!$this->preAction($request, 0, ['checkPrivilages' => 1] )) {
            return $this->responseAccessDenied();
        }
        $tabs=[];
        $tabsOpt=[];
        $i=0;
        $clientIdPrototype=$this->getEntityHelper()->getIdPrototype(static::ec);
        foreach([ 'Orders','Deliveries', 'Invoices', 'PriceLists', 'Settings' ] as $tec){
            $ten=$this->getEntityHelper()->getEntityName($tec);
            $tabs['client_'.$ten]=$this->genClientServicePanel($tec, $clientIdPrototype, $i==0);
            $tabsOpt[$ten]=['ajax' => false];
            $this->addEntityModal($tec);
            $i++;
        }
        $tabsOpt['edit']=['ajax' => true];
        $cEditUrl=$this->getUrl('edit', static::ec, ["id" => $clientIdPrototype, "type" => "p"]);
        $tabs['client_edit'] = $this->genPanel(static::ec, [
            'label' => $this->getTransHelper()->labelText('edit'),
            'd' => [
                'url' => json_encode([
                    'start' => $cEditUrl,
                    'edit' => $cEditUrl,
                    'new' => $this->getUrl('new', static::ec, [ "type" => "p"])
                ])               
            ],
            'attr' =>[
                'id' => 'edit_panel'
            ]
        ]);
        $this->setTemplate('service', null)
            ->setRenderOptions([
                'title' => $this->getTransHelper()->titleText('service'),
                'panel_left' => $this->genPanel(static::ec, [
                    'content' => $this->tmplPath('panel', null),
                    'toolbars' => [
                        $this->genToolbar(),
                        $this->getFilterbarGenerator()->generate('service', static::ec)
                    ],
                    'table' => $this->getDTGenerator()->generate('panel', static::ec, [
                        'actions' => true, 
                        'select' => 'single',
                        'ajax' => [
                            'urlType' => 'ajax_details'
                        ]
                        // 'd' => [
                        //     'ajax' => [
                        //         'url' => $this->getUrl('ajax_details', static::ec)
                        //     ],
                        //     'filters' => $this->getFiltersGenerator()->generate('table_service')
                        // ]
                    ])
                ] ),
                'panel_right' => [
                    'template' => $this->tmplPath('panel_service', null),
                    'tabs'=> [
                        'panels' => $tabs
                    ],
                    'attr' =>[
                        'id' => 'service_panel'
                    ],
                    'd' => [
                        'options' => json_encode([
                            'panels' => $tabsOpt
                        ])
                    ]
                ],
                'service' => [
                    'attr' => [
                        'id' => 'service'
                    ]
                ]
            ])
            ->addEntityModal();
        return $this->renderSystem();
    }
   
}