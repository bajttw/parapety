<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class OrdersType extends AbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options){
        $admin_writeable = ($options['form_admin'] ? '1' : '0');
        $client_readonly = ($options['form_admin'] ? '0' : '1');
        if ($options['client_choice']){
            $builder
            ->add('client', EntityType::class, [
                'class' => 'AppBundle:Clients',
                'label' => 'orders.label.client',
                'choice_label' => 'name',   
                'placeholder' => 'orders.label.choiceClient',   
                'attr' =>   [
                    'title' => 'clients.title.choice',
                    'data-options' => json_encode([
                        'widget' => [
                            'type' => 'multiselect'
                        ]
                    ])    
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                            ->where('c.active = true')
                        ->orderBy('c.name', 'ASC');
                }                
            ]);
        }else{
            $builder->add('client', EntityHiddenType::class, [
                'entity_class' => "AppBundle\\Entity\\Clients",
                'label' => 'orders.label.client',
                'attr' => [
                    'readonly' => 'readonly',
                    'title' => 'orders.title.client',
                    'data-type' => 'json',
                    'data-options' => json_encode([
                        'disp' => [
                            'type' => 'n'
                        ]
                    ])    
                ],
            ]);
        }
        $builder
            // ->add('id', HiddenType::class, [
            //     'required' => false
            // ])
            ->add('number', HiddenType::class, [
                'required' => false,
                'label' => "orders.label.number",
                'attr' => [
                ]
            ])
            ->add('clientNumber', null, [
                'required' => false,
                'label' => "orders.label.clientNumber",
                'attr' => [
                    'title' => 'orders.title.clientNumber',
                ]
            ])
            ->add('express', HiddenType::class, [
                'label' => 'orders.label.express',
                'attr' => [
                    'title' => 'orders.title.express',
                    'data-type' => 'number'
                ]
            ])
            ->add('status', HiddenType::class, [
                'label' => "orders.label.status",
                'attr' => [
                    'title' => 'orders.title.status',
                    'data-type' => 'number'
                ]    
            ])
            ->add('model', EntityHiddenType::class, [
                'entity_class' => "AppBundle\\Entity\\Models",
                'label' => 'orders.label.model',
                'attr' => [
                    'title' => 'orders.title.model',
                    'data-type' => 'json'
                ]
            ])
            ->add('size', EntityHiddenType::class, [
                'entity_class' => "AppBundle\\Entity\\Sizes",
                'label' => 'orders.label.size',
                'attr' => [
                    'title' => 'orders.title.size',
                    'data-type' => 'json'
                ]
            ])
            // ->add('trim', EntityHiddenType::class, [
            //     'class' => "AppBundle:Trims",
            //     'label' => 'orders.label.trim',
            //     'attr' => [
            //         'title' => 'orders.title.trim',
            //         'data-type' => 'json'
            //     ],
            // ])
            ->add('color', EntityHiddenType::class, [
                'entity_class' => "AppBundle\\Entity\\Colors",
                'label' => 'orders.label.color',
                'attr' => [
                    'title' => 'orders.title.color',
                    'data-type' => 'json'
                ]
            ])
            ->add('trims', HiddenType::class, [
                'label' => 'orders.label.trims',
                'attr' => [
                    'title' => 'orders.title.trims',
                    'data-type' => 'astring'
                ]
            ])
            ->add(
                $builder->create('created', HiddenType::class, [
                    'required' => false,
                    'label' => "orders.label.created",
                    'attr' => [
                        'title' => 'orders.title.created'
                    ]
                ])
                ->addModelTransformer(new DateTimeToStringTransformer(null, null, 'Y-m-d H:i'))
            )
            ->add(
                $builder->create('approved', HiddenType::class, [
                    'required' => false,
                    'label' => "orders.label.approved",
                    'attr' => [
                        'title' => 'orders.title.approved',
                    ]
                ])
                ->addModelTransformer(new DateTimeToStringTransformer(null, null, 'Y-m-d H:i'))
            )
            ->add(
                $builder->create('term', TextType::class, [
                    'required' => false,
                    'label' => "orders.label.term",
                    'attr' => [
                        'title' => 'orders.title.term'
                    ]
                ])
                ->addModelTransformer(new DateTimeToStringTransformer(null, null, 'Y-m-d'))
            )
            ->add('quantity', HiddenType::class, [
                'required' => false,
                'label' => "orders.label.quantity",
                'attr' => [
                    'data-type' => 'number'
                ]
            ])
            ->add('area', HiddenType::class, [
                'required' => false,
                'label' => "orders.label.area",
                'attr' => [
                    'data-type' => 'float'
                ]
            ])
            ->add('clientComment', TextareaType::class, [
                'required' => false,
                'label' => 'orders.label.clientComment',
                // 'read_only' => $admin_writeable,
                'attr' => [
                    'title' => 'orders.title.clientComment',
                    'data-options' => json_encode([
                        'readonly' => $admin_writeable,
                        'control' => [
                            'text' => 'W'
                        ]
                    ])    
                ],
            ]) 
            ->add('orderComment', TextareaType::class, [
                'required' => false,
                'label' => 'orders.label.orderComment',
                // 'read_only' => $admin_writeable,
                'attr' => [
                    'read_only' => $admin_writeable,
                    'title' => 'orders.title.orderComment',
                    'data-options' => json_encode([
                        'readonly' => $admin_writeable,
                        'control' => [
                            'text' => 'K'
                        ]
                    ])    
                ],
            ]) 
            ->add('prodComment', TextareaType::class, [
                'required' => false,
                'label' => 'orders.label.prodComment',
                'attr' => [
                    'read_only' => $client_readonly,
                    'title' => 'orders.title.prodComment',
                    'data-options' => json_encode([
                        'readonly' => $client_readonly,
                        'control' => [
                            'text' => 'P'
                        ]
                    ])    
                ],
            ]) 
            ->add('positions', CollectionType::class, [
                'entry_type' => PositionsType::class,
                'entry_options'=> [
                    'form_admin' => $options['form_admin'],  
                    'em' => $options['em'],
                    'label' => false,
                    'entities_settings' => $options['entities_settings']
                ],
                'attr' => [
                    'data-prototype-name' => '__pn__'
                ],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__pn__',
                // Post update
                'by_reference' => false,
                'error_bubbling' => true
            ]) 

        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Orders',
            'form_admin' => false,
            'client_choice' => false,
            'em' => null,
            'translator' => null,
            'entities_settings' => null

        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {    
        parent::buildView($view, $form, $options);
        $view->vars['exp_buttons'] = [ 
            'export' => [
                'action' => 'copy',
                'icon' => 'content_copy',
                'd' => [
                    'exp' => ''
                ]
            ],
            'pdf' => [
                'action' => 'pdf',
                'icon' => 'picture_as_pdf',
                'd' => [
                    'exp' => 'order'
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(){
        return 'appbundle_orders';
    }
}
