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

class PricesType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options){
        $admin_writeable = ($options['form_admin'] ? '1' : '0');
        $validation = array_key_exists('validation', $options['entities_settings']['Prices']) ? $options['entities_settings']['Prices']['validation'] : [];
        $builder
            ->add('priceListItem', EntityHiddenType::class, [
                'entity_class' => "AppBundle\\Entity\\PriceListItems",
                'required' => true,
                'label' => false,
                'attr' => [
                    'title' => 'prices.title.priceListItem',
                    'data-type' => 'json',
                    'data-item' => '1'
                ]
            ])
            ->add(
                'value', 
                null, 
                [
                    'label' => false, 
                    'required' => true,
                    'attr' => [
                        'autocomplete'=>'off',
                        'data-type' => 'float',
                        'data-edit' => '1',
                        'data-name' => 'val'
                    ]                        
                ]
            )
        ;
        
  }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Prices',
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
        return 'appbundle_prices';
    }
}
