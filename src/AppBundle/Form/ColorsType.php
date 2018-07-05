<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use AppBundle\Entity\Colors;

class ColorsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sequence', null, [
                'label' => 'colors.label.sequence',
                'attr' => [
                    'title' => 'colors.title.sequence'
                ]
            ])
            ->add('name', null, [
                'label' => 'colors.label.name',
                'attr' => [
                    'title' => 'colors.title.name'
                ]
            ])
            ->add('symbol', null, [
                'label' => 'colors.label.symbol',
                'attr' => [
                    'title' => 'colors.title.symbol'
                ]
            ])
            ->add('description', null, [
                'label' => 'colors.label.description',
                'attr' => [
                    'title' => 'colors.title.description'
                ]
            ])
            ->add('active', SwitchType::class, [
                'label' => 'colors.label.active',
                'attr' => [
                    'title' => 'colors.title.active'
                ],
                'entity_name' => 'colors'
            ])
            ->add('upload', UploadType::class, [
                'label' => 'colors.label.upload',
                'attr'=> [
                    'title' => 'colors.title.upload',
                    'preview' => true,
                    'data-options' => json_encode([
                        'disp' => true,
                        'widget' => [
                            'type' => 'upload',
                            'options' => [
                                'single' => true,
                                'preview' =>  $options['entities_settings']['Colors']['image'],
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
            'data_class' => Colors::class,
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
        return 'appbundle_colors';
    }
}
