<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Type\Product;

//use Kemoc\Storehouse\ApiBundle\Entity\Item;
//use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form;
//use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class CreateType extends Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', Type\TextType::class)
            ->add('amount', Type\IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            [
                //'data_class' => Item::class,
                'csrf_protection' => false,
            ]
        );
    }
}

