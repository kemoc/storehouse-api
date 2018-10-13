<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Data\Product;

//use Doctrine\Common\Collections\ArrayCollection;
use Kemoc\Storehouse\ApiBundle\Entity\Product as ProductEntity;
//use Symfony\Component\Validator\Constraints as Assert;
//use Symfony\Component\Translation\TranslatorInterface;

class Update implements UpdateInterface
{
    use CreateUpdateTrait;

    /**
     * @var int
     */
    protected $id = 0;

    public function __construct(ProductEntity $productEntity)
    {
        $this->setProductEntity($productEntity);
        $this->id = $productEntity->getId();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->getProductEntity()->getId();
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        //$this->id = $id;
    }



}

