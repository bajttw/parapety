<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PriceListItemType extends AbstractType
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
                    'label' => false
                ]
            )
            ->add(
                'symbol', 
                null, 
                [
                    'label' => false, 
                ]
            )
            ->add(
                'price', 
                null, 
                [
                    'label' => false, 
                    'required' => true,
                    'attr' => [
                        'autocomplete'=>'off',
                        'data-type' => 'float'
                    ]                        
                ]
            )
            ->add('size', EntityHiddenType::class, [
                'entity_class' => "AppBundle\\Entity\\Sizes",
                'label' => false,
                'attr' => [
                    'title' => 'pricelistitems.title.size',
                    'data-type' => 'json'
                ]
            ])
            ->add('color', EntityHiddenType::class, [
                'entity_class' => "AppBundle\\Entity\\Colors",
                'label' => false,
                'attr' => [
                    'title' => 'pricelistitems.title.color',
                    'data-type' => 'json'
                ]
            ]);
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
        return 'appbundle_pricelistitem';
    }
}
