<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ModelsType extends AbstractType
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
                    'label' => 'models.label.sequence'
                ]
            )
            ->add(
                'name', 
                null, 
                [
                    'label' => "models.label.name"
                ]
            )
            ->add(
                'symbol', 
                null, 
                [
                    'label' => "models.label.symbol", 
                ]
            )
            ->add(
                'description', 
                null, 
                [
                    'label' => "models.label.description"
                ]
            )
            ->add('active', SwitchType::class, [
                'label' => 'models.label.active',
                'entity_name' => 'models'
            ])
            ->add('upload', UploadType::class, [
                'label' => 'models.label.upload',
                'attr'=> [
                    'preview' => true,
                    'data-options' => json_encode([
                        'disp' => true,
                        'widget' => [
                            'type' => 'upload',
                            'options' => [
                                'single' => true,
                                'preview' =>  $options['entities_settings']['Models']['image'],
                            ]
                        ]
                    ])    
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {    
        parent::buildView($view, $form, $options);
        $view->vars['attr']['data-form-fields'] = json_encode([ 'sequence', 'name', 'symbol', 'description', 'active', 'upload' ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Models',
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
        return 'appbundle_models';
    }
}
