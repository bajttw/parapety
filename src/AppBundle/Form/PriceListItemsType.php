<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PriceListItemsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name', 
                null, 
                [
                    'label' => "pricelistitems.label.name"
                ]
            )
            ->add(
                'symbol', 
                null, 
                [
                    'label' => "pricelistitems.label.symbol", 
                ]
            )
            ->add(
                'price', 
                null, 
                [
                    'label' => "pricelistitems.label.price", 
                    'required' => true,
                    'attr' => [
                        'autocomplete'=>'off',
                        'data-type' => 'float'
                    ]                        
                ]
            )
            ->add('size', EntityHiddenType::class, [
                'entity_class' => "AppBundle\\Entity\\Sizes",
                'label' => 'pricelistitems.label.size',
                'attr' => [
                    'title' => 'pricelistitems.title.size',
                    'data-type' => 'json'
                ]
            ])
            ->add('color', EntityHiddenType::class, [
                'entity_class' => "AppBundle\\Entity\\Colors",
                'label' => 'pricelistitems.label.color',
                'attr' => [
                    'title' => 'pricelistitems.title.color',
                    'data-type' => 'json'
                ]
            ])
            ->add('active', SwitchType::class, [
                'label' => 'pricelistitems.label.active',
                'entity_name' => 'pricelistitems'
            ])
            ;
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
            'data_class' => 'AppBundle\Entity\PriceListItems',
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
        return 'appbundle_pricelistitems';
    }
}
