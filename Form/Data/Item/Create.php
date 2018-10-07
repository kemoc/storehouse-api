<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Data\Item;

//use Doctrine\Common\Collections\ArrayCollection;
use Kemoc\Storehouse\ApiBundle\Entity\Item as ItemEntity;
//use Symfony\Component\Validator\Constraints as Assert;
//use Symfony\Component\Translation\TranslatorInterface;

class Create implements CreateInterface
{
    use CreateUpdateTrait;

    public function __construct(ItemEntity $itemEntity)
    {
        $this->setItemEntity($itemEntity);

    }


}

