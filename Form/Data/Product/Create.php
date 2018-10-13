<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Data\Product;

//use Doctrine\Common\Collections\ArrayCollection;
use Kemoc\Storehouse\ApiBundle\Entity\Product as ProductEntity;
//use Symfony\Component\Validator\Constraints as Assert;
//use Symfony\Component\Translation\TranslatorInterface;

class Create implements CreateInterface
{
    use CreateUpdateTrait;

    public function __construct(ProductEntity $productEntity)
    {
        $this->setProductEntity($productEntity);

    }


}

