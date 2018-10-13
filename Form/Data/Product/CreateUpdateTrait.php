<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Data\Product;

use Symfony\Component\Validator\Constraints as Assert;
//use Doctrine\Common\Collections\ArrayCollection;
use Kemoc\Storehouse\ApiBundle\Entity\Product as ProductEntity;
//use Symfony\Component\Translation\TranslatorInterface;

trait CreateUpdateTrait
{

    /**
     * @var ProductEntity
     */
    protected $productEntity;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="255")
     * @var string
     */
    protected $name = "";

    /**
     * @Assert\NotNull()
     * @Assert\Range(min="0")
     * @var int
     */
    protected $amount = 0;

    /**
     * @return ProductEntity
     */
    public function getProductEntity(): ProductEntity
    {
        return $this->productEntity;
    }

    /**
     * @param ProductEntity $productEntity
     */
    public function setProductEntity(ProductEntity $productEntity): void
    {
        $this->productEntity = $productEntity;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
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


    public function copyTo(ProductEntity $product)
    {
        $product->setName($this->getName());
        $product->setAmount($this->getAmount());
    }
}

