<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PriceListsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $client_readonly = ($options['form_admin'] ? '0' : '1');
        if ($options['clients']) {
            $builder
            ->add('clients', EntityType::class, [
                'class' => 'AppBundle:Clients',
                'by_reference' => false,
                'label' => 'pricelists.label.clients',
                'multiple' => true,
                'choice_label' => 'name',
                'placeholder' => 'pricelists.label.choiceClients',
                'attr' => [
                    'title' => 'clients.title.choice',
                    'data-options' => json_encode([
                        'widget' => [
                            'type' => 'multiselect'
                        ]
                    ])
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                            ->where('c.active = true')
                        ->orderBy('c.name', 'ASC');
                }
            ]);
        } else {
            $builder
            ->add('clientsGroups', EntityType::class, [
                'class' => 'AppBundle:ClientsGroups',
                'by_reference' => false,
                'label' => 'pricelists.label.clientsGroups',
                'multiple' => true,
                'choice_label' => 'name',
                'placeholder' => 'pricelists.label.choiceClientsGroups',
                'attr' => [
                    'title' => 'clientsgroups.title.choice',
                    'data-options' => json_encode([
                        'widget' => [
                            'type' => 'multiselect'
                        ]
                    ])
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                }
            ]);
        }

        $builder
            ->add(
                'title',
                null,
                [
                    'label' => 'pricelists.label.title'
                ]
            )
            ->add(
                'description',
                null,
                [
                    'required' => false,
                    'label' => 'pricelists.label.description'
                ]
            )
            ->add(
                $builder->create('start', TextType::class, [
                    'label' => 'pricelists.label.start',
                    'attr' => [
                        'title' => 'pricelists.title.start'
                    ]
                ])
                ->addModelTransformer(new DateTimeToStringTransformer(null, null, 'Y-m-d H:i'))
            )
            ->add(
                $builder->create('end', TextType::class, [
                    'required' => false,
                    'label' => 'pricelists.label.end',
                    'attr' => [
                        'title' => 'pricelists.title.end'
                    ]
                ])
                ->addModelTransformer(new DateTimeToStringTransformer(null, null, 'Y-m-d H:i'))
            )
            ->add('prices', CollectionType::class, [
                'attr' => [
                    'data-prototype-name' => '__pn__'
                ],
                'entry_type' => PricesType::class,
                'entry_options' => [
                    'form_admin' => $options['form_admin'],
                    'em' => $options['em'],
                    'label' => false,
                    'entities_settings' => $options['entities_settings'],
                    'attr' => [
                        'data-options' => json_encode([
                            'actions' => false])
                        ]
                    ],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__pn__',
                // Post update
                'by_reference' => false,
                'error_bubbling' => true
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
            'data_class' => 'AppBundle\Entity\pricelists',
            'form_admin' => false,
            'em' => null,
            'entities_settings' => null,
            'translator' => null,
            'clients' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_pricelists';
    }
}
