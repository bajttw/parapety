<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class PriceListItemsGenerateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $_getChoices=function($dic){
            $choices=[];
            foreach($dic as $r){
                $choices[$r['n']]=$r['v'];
            }
            return $choices;
        };
        $builder
            ->add('size',ChoiceType::class, [
                'label' => 'pricelistitems.label.size',
                'multiple' => true,
                'choices' => $_getChoices($options['entities_settings']['Orders']['dictionaries']['Sizes']),
                'choice_translation_domain' => false,
                'attr' => [
                    // 'data-dic' => json_encode($options['entities_settings']['Orders']['dictionaries']['Sizes']),
                    'title' => 'pricelistitems.title.size'
                ],
                'required' => false
            ])
            ->add('color', ChoiceType::class, [
                'label' => 'pricelistitems.label.color',
                'choices' => $_getChoices($options['entities_settings']['Orders']['dictionaries']['Colors']),
                'choice_translation_domain' => false,
                'multiple' => true,
                'attr' => [
                    // 'data-dic' => json_encode($options['entities_settings']['Orders']['dictionaries']['Colors']),
                    'title' => 'pricelistitems.title.color'
                ],
                'required' => false
            ])
            ->add('items', CollectionType::class, [
                'attr' => [
                    'data-prototype-name' => '__pn__'
                ],
                'entry_type' => PriceListItemType::class,
                'entry_options'=> [
                    'form_admin' => $options['form_admin'],  
                    'em' => $options['em'],
                    'label' => false,
                    'entities_settings' => $options['entities_settings']
                ],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__pn__',
                // Post update
                'by_reference' => false,
                'error_bubbling' => true
            ])  ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {    
        parent::buildView($view, $form, $options);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'stdClass',
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
        return 'appbundle_pricelistitemsgenerate';
    }
}
