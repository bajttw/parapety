<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SizesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'sequence', 
                null, 
                [
                    'label' => 'sizes.label.sequence'
                ]
            )
            ->add(
                'name', 
                null, 
                [
                    'label' => "sizes.label.name"
                ]
            )
            ->add(
                'symbol', 
                null, 
                [
                    'label' => "sizes.label.symbol"
                ]
            )
            ->add(
                'description', 
                null, 
                [
                    'label' => "sizes.label.description"
                ]
            )
            ->add('active', SwitchType::class, [
                'label' => "sizes.label.active",
                'entity_name' => 'sizes',
            ])
                
       ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {    
        parent::buildView($view, $form, $options);
        $view->vars['attr']['data-form-fields'] = json_encode([ 'name', 'symbol', 'sequence', 'description', 'active']);
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Sizes',
            'form_admin' => false,            
            'em' => null,
            'translator' => null,
            'entities_settings' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_sizes';
    }
}
