<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Doctrine\ORM\EntityRepository;

class PositionsType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options){
        $admin_writeable = ($options['form_admin'] ? '1' : '0');
        $client_readonly = ($options['form_admin'] ? '0' : '1');
        $validation = array_key_exists('validation', $options['entities_settings']['Positions']) ? $options['entities_settings']['Positions']['validation'] : [];
        $builder
            // ->add('id', HiddenType::class, [
            //     'required' => false
            // ])
            ->add('nr', HiddenType::class,[
                'attr' => [
                    'data-type' => 'number',
//                    'data-options' => json_encode([
//                        'disp' => [
//                            'type' => 'v',
//                            'prototype' => "<span>__v__</span>"
//                        ]
//                    ])
                ]    
            ]) 
            ->add('lengthcm', NumberType::class,[
                'required' => true,
                'label' => false,
                'attr' => [
                    'autocomplete'=>'off',
                    'data-type' => 'float',
//                    'data-options' => json_encode([
//                        'check_key' => '1',
////                        'validation' => array_key_exists('length', $validation) ? $validation['length'] : []
//                        'navi' => '1',
//                        'calc' => '1'
//                    ])
                ]
            ])
            ->add('widthcm', NumberType::class,  [
                'required' => true,
                'label' => false,
                'attr' => [
                    'autocomplete'=>'off',
                    'data-type' => 'float',
//                    'data-options' => json_encode([
//                        'check_key' => '1',
////                        'validation' => array_key_exists('width', $validation) ? $validation['width'] : []
//                        'navi' => '2',
//                        'calc' => '1',
//                    ])
                ]
            ])
            ->add('quantity', NumberType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'autocomplete'=>'off',
                    'data-type' => 'number',
//                    'data-options' => json_encode([
//                        'check_key' => '1',
////                        'validation' => array_key_exists('quantity', $validation) ? $validation['quantity'] : []
//                        'navi' => '3',
//                        'calc' => '1',
//                    ])
                ]
            ])
            ->add('model', EntityHiddenType::class, [
                'entity_class' => "AppBundle\\Entity\\Models",
                'required' => true,
                'attr' => [
                    'title' => 'positions.title.model',
                    'data-type' => 'json',
                    'data-options' => json_encode([
                        'control' => [
//                            'type' => 'modal',
//                            'modal' => '1',
                            'text' => 'M',
//                            'signal' => '2',
//                            'navi' => '1'
                        ],
//                        'disp' => [
//                            'type' => 'n',
//                            'prefix' => 'btn',
//                            'default' => 'M'
//                        ]
                    ])
                ],    
                'label' => false
            ])
            ->add('color', EntityHiddenType::class, [
                'entity_class' => "AppBundle\\Entity\\Colors",
                'required' => true,
                'attr' => [
                    'title' => 'positions.title.color',
                    'data-type' => 'json',
                    'data-options' => json_encode([
                        'control' => [
                            'text' => 'K',
                        ]
                    ])
                ],
                'label' => false
                ]
            )
            ->add('size', EntityHiddenType::class, [
                'entity_class' => "AppBundle\\Entity\\Sizes",
                'required' => true,
                'attr' => [
                    'title' => 'positions.title.size',
                    'data-type' => 'json',
                    'data-options' => json_encode([
                        'control' => [
//                            'type' => 'modal',
//                            'modal' => '1',
                            'text' => 'G',
//                            'signal' => '2',
                        ],
//                        'disp' => [
//                            'type' => 'n',
//                            'default' => 'G',
//                            'prefix' => 'btn'
//                        ]
                    ])
                ],
                'label' => false
            ])
            ->add('trims', HiddenType::class, [
                'required' => true,
                'attr' => [
                    'title' => 'positions.title.trims',
                    'data-type' => 'astring',
                    'data-options' => json_encode([
                        'control' => [
                            'text' => 'O'
                        ]
                    ])
                ],
                'label' => false
            ])
            ->add('area', HiddenType::class, [
                'attr' => [
                    'data-type' => 'float'
                ]
            ])
            ->add('clientComment', TextareaType::class, [
                'required' => false,
                'label' => 'positions.label.clientComment',
                'attr' => [
                    'title' => 'positions.title.clientComment',
                    'data-options' => json_encode([
                        'readonly' => $admin_writeable,
                        'control' => [
                            'text' => 'W'
                        ]
                    ])
                ]
            ])
            ->add('positionComment', TextareaType::class, [
                'required' => false,
                'label' => 'positions.label.positionComment',
                'attr' => [
                    'read_only' => $admin_writeable,
                    'title' => 'positions.title.positionComment',
                    'data-options' => json_encode([
                        'readonly' => $admin_writeable,
                        'control' => [
                            'text' => 'K'
                        ]
                    ])
                ]
            ])
            ->add('prodComment', TextareaType::class, [
                'required'  => false,
                'label' => 'positions.label.prodComment',
                'attr' => [
                    'read_only' => $client_readonly,
                    'title' => 'positions.title.prodComment',
                    'data-options' => json_encode([
                        'readonly' => $client_readonly,
                        'control' => [
                            'text' => 'P'
                        ]
                    ])
                ]
            ]) 
               ;
        ;

        // $builder->add('uploads', UploadsType::class,[
        //     'label' => 'positions.label.uploads'
        // ]);
        
        $builder->add('uploads', UploadsType::class,[
            'label' => 'positions.label.uploads',
            'en' => 'positions'
            // 'attr' => [
            //     'title' => 'positions.title.attach'
            // ]
        ]);
        
  }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Positions',
            'form_admin' => false,
            'em' => null,
            'entities_settings' => null,
            'cascade_validation' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(){
        return 'appbundle_positions';
    }
}
