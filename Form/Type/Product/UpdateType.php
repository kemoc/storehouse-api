<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Type\Product;

//use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form;
//use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class UpdateType extends CreateType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('id', Type\IntegerType::class, [
                'required' => false
            ])
        ;
        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
    }
}

