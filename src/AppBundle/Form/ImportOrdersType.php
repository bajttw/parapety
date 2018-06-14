<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ImportOrdersType extends AbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options){
        $admin_writeable = ($options['form_admin'] ? '1' : '0');
        $client_readonly = ($options['form_admin'] ? '0' : '1');
        if ($options['client_choice']){
            $builder
            ->add('client', EntityType::class, [
                'class' => 'AppBundle:Clients',
                'label' => 'orders.label.client',
                'choice_label' => 'name',   
                'placeholder' => 'orders.label.choiceClient',   
                'attr' =>   [
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
        }else{
            $builder->add('client', EntityHiddenType::class, [
                'entity_class' => "AppBundle\\Entity\\Clients",
                'label' => 'orders.label.client',
                'attr' => [
                    'readonly' => 'readonly',
                    'title' => 'orders.title.client',
                    'data-type' => 'json',
                    'data-options' => json_encode([
                        'disp' => [
                            'type' => 'n'
                        ]
                    ])    
                ],
            ]);
        }
        $builder
            ->add('type', HiddenType::class, [
                'label' => 'orders.label.importType',
                'attr' => [
                    'title' => 'orders.title.importType',
                    'data-type' => 'number'
                ]
            ])
            ->add('content', TextareaType::class, [
                'required' => false,
                'label' => "orders.label.importContent",
                'attr' => [
                    'title' => 'orders.title.importContent',
                ]    
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'form_admin' => false,
            'client_choice' => false,
            'em' => null,
            'translator' => null,
            'entities_settings' => null

        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {    
        parent::buildView($view, $form, $options);
        $view->vars['attr']['data-form-fields'] = json_encode([ 'client', 'type', 'content' ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(){
        return 'appbundle_orders';
    }
}
