<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Form\DataTransformer\EntityToHiddenTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AppBundle\Utils\Utils;

class OrderHiddenType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * {@inheritdoc}
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $builder->addModelTransformer(new EntityToHiddenTransformer($this->objectManager, $options['class']));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'entity_class' => "AppBundle\\Entity\\Orders",
                'get_data_options' => [ ],
                'form_admin' => false,
                'em' => null,
                'invalid_message' => 'The entity does not exist.'

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return EntityHiddenType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'order_hidden';
    }

}
