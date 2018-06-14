<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AppBundle\Entity\Productions;

class ProductionsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', HiddenType::class, [
                'required' => false,
                'label' => "productions.label.number",
                'attr' => [
                ]
            ])
            ->add(
                $builder->create('generated', HiddenType::class, [
                    'required' => false,
                    'label' => "productions.label.generated",
                    'attr' => [
                        'title' => 'productions.title.generated'
                    ]
                ])
                ->addModelTransformer(new DateTimeToStringTransformer(null, null, 'Y-m-d H:i'))
            )
            ->add('progress', HiddenType::class, [
                'label' => "productions.label.progress",
                'attr' => [
                    'title' => 'productions.title.progress',
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
                        'en' => Productions::en
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
                    'exp' => 'production'
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
            'data_class' => 'AppBundle\Entity\Productions',
            'form_admin' => false,            
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
        return 'appbundle_productions';
    }
}
