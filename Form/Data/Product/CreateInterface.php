<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Data\Product;

//use Symfony\Component\Validator\Constraints as Assert;
use Kemoc\Storehouse\ApiBundle\Entity\Product as ProductEntity;
//use Symfony\Component\Translation\TranslatorInterface;

interface CreateInterface
{

    public function __construct(ProductEntity $productEntity);

    public function setProductEntity(ProductEntity $productEntity): void;

    public function getProductEntity(): ProductEntity;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getAmount(): int;

    public function setAmount(int $amount): void;

}

