<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Type\Item;

//use Kemoc\Storehouse\ApiBundle\Entity\Item;
//use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form;
//use Symfony\Component\Form\Extension\Core\Type;

class CreateType extends Form\AbstractType
{
    use CreateUpdateTrait;
}

