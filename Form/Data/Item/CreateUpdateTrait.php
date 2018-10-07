<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Data\Item;

use Symfony\Component\Validator\Constraints as Assert;
//use Doctrine\Common\Collections\ArrayCollection;
use Kemoc\Storehouse\ApiBundle\Entity\Item as ItemEntity;
//use Symfony\Component\Translation\TranslatorInterface;

trait CreateUpdateTrait
{

    /**
     * @var ItemEntity
     */
    protected $itemEntity;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="255")
     * @var null|string
     */
    protected $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Range(min="0")
     * @var int
     */
    protected $amount = 0;

    /**
     * @return ItemEntity
     */
    public function getItemEntity(): ItemEntity
    {
        return $this->itemEntity;
    }

    /**
     * @param ItemEntity $itemEntity
     */
    public function setItemEntity(ItemEntity $itemEntity): void
    {
        $this->itemEntity = $itemEntity;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }


    public function copyTo(ItemEntity $item)
    {
        $item->setName($this->getName());
        $item->setAmount($this->getAmount());
    }
}

