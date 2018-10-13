<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Data\Product;

//use Symfony\Component\Validator\Constraints as Assert;
use Kemoc\Storehouse\ApiBundle\Entity\Product as ProductEntity;
//use Symfony\Component\Translation\TranslatorInterface;

interface UpdateInterface extends CreateInterface
{

    public function getId(): int;

}

