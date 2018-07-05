<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use AppBundle\Entity\Trims;

class TrimsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sequence', null, [
                'label' => 'trims.label.sequence',
                'attr' => [
                    'title' => 'trims.title.sequence'
                ]
            ])
            ->add('name', null, [
                'label' => 'trims.label.name',
                'attr' => [
                    'title' => 'trims.title.name'
                ]
            ])
            ->add('symbol', null, [
                'label' => 'trims.label.symbol',
                'attr' => [
                    'title' => 'trims.title.symbol'
                ]
            ])
            ->add('description', null, [
                'label' => 'trims.label.description',
                'attr' => [
                    'title' => 'trims.title.description'
                ]
            ])
            ->add('active', SwitchType::class, [
                'label' => 'trims.label.active',
                'attr' => [
                    'title' => 'trims.title.active'
                ],
                'entity_name' => 'trims'
            ])
            ->add('upload', UploadType::class, [
                'label' => 'trims.label.upload',
                'attr'=> [
                    'title' => 'trims.title.upload',
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
            'data_class' => Trims::class,
            'form_admin' => false,            
            'em' => null,
            'entities_settings' => [],
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
