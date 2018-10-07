<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Data\Item;

//use Doctrine\Common\Collections\ArrayCollection;
use Kemoc\Storehouse\ApiBundle\Entity\Item as ItemEntity;
//use Symfony\Component\Validator\Constraints as Assert;
//use Symfony\Component\Translation\TranslatorInterface;

class Update implements UpdateInterface
{
    use CreateUpdateTrait;

    /**
     * @var int
     */
    protected $id = 0;

    public function __construct(ItemEntity $itemEntity)
    {
        $this->setItemEntity($itemEntity);

    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->getItemEntity()->getId();
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }



}

