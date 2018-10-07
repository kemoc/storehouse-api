<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Data\Item;

//use Symfony\Component\Validator\Constraints as Assert;
use Kemoc\Storehouse\ApiBundle\Entity\Item as ItemEntity;
//use Symfony\Component\Translation\TranslatorInterface;

interface CreateInterface
{

    public function __construct(ItemEntity $itemEntity);

    public function setItemEntity(ItemEntity $itemEntity): void;

    public function getItemEntity(): ItemEntity;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getAmount(): int;

    public function setAmount(int $amount): void;

}

