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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AppBundle\Entity\Deliveries;

class DeliveriesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $admin_writeable = ($options['form_admin'] ? '1' : '0');
        if ($options['client_choice']){
            $builder
            ->add('client', EntityType::class, [
                'class' => 'AppBundle:Clients',
                'label' => 'deliveries.label.client.name',
                'choice_label' => 'name',   
                'placeholder' => 'deliveries.label.choiceClient',   
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
                'label' => 'deliveries.label.client.name',
                'attr' => [
                    'readonly' => 'readonly',
                    'title' => 'deliveries.title.client',
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
            ->add('number', HiddenType::class, [
                'required' => false,
                'label' => "deliveries.label.number",
                'attr' => [
                ]
            ])
            ->add(
                $builder->create('generated', HiddenType::class, [
                    'required' => false,
                    'label' => "deliveries.label.generated",
                    'attr' => [
                        'title' => 'deliveries.title.generated'
                    ]
                ])
                ->addModelTransformer(new DateTimeToStringTransformer(null, null, 'Y-m-d H:i'))
            )
            ->add('progress', HiddenType::class, [
                'label' => "deliveries.label.progress",
                'attr' => [
                    'title' => 'deliveries.title.progress',
                    'data-type' => 'number'
                ]    
            ])
            ->add('orders', CollectionType::class, [
                'entry_type' => OrderHiddenType::class,
                'entry_options'=> [
                    'form_admin' => $options['form_admin'],  
                    'em' => $options['em'],
                    'label' => false,
                    'get_data_options' =>[
                        'en' => Deliveries::en
                    ]
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
    public function buildView(FormView $view, FormInterface $form, array $options)
    {    
        parent::buildView($view, $form, $options);
        $view->vars['exp_buttons'] = [ 
            'xls' => [
                'action' => 'xls',
                'icon' => 'picture_as_pdf',
                'd' => [
                    'exp' => 'delivery'
                ]
            ]
        ];        
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Deliveries',
            'form_admin' => false,            
            'client_choice' => false,
            'em' => null,
            'entities_settings' =>null,
            'translator' =>null
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_deliveries';
    }
}
