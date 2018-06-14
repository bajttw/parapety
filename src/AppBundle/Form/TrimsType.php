<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
class TrimsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sequence', null, ['label' => 'trims.label.sequence'])
            ->add('name', null, ['label' => 'trims.label.name'])
            ->add('symbol', null, ['label' => 'trims.label.symbol'])
            ->add('description', null, ['label' => 'trims.label.description'])
            ->add('active', SwitchType::class, [
                'label' => 'trims.label.active',
                'entity_name' => 'trims'
            ])
            ->add('upload', UploadType::class, [
                'label' => 'trims.label.upload',
                'attr'=> [
                    'preview' => true,
                    'data-options' => json_encode([
                        'show' => true,
                        'widget' => [
                            'type' => 'upload',
                            'options' => [
                                'single' => true,
                                'preview' =>  $options['entities_settings']['Trims']['image'],
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
            'data_class' => 'AppBundle\Entity\Trims',
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
        return 'appbundle_trims';
    }
}
