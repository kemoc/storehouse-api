<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Entity;

//use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Item
 * @package Kemoc\Storehouse\ApiBundle\Entity
 *
 * @ORM\Table(name="item",indexes={@ORM\Index(name="amount_idx", columns={"amount"})})
 * @ORM\Entity(repositoryClass="Kemoc\Storehouse\ApiBundle\Repository\ItemRepository")
 */
class Item
{
    const NUM_ITEMS = 25;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     */
    private $name = "";
    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="integer", options={"default": 0})
     * @Assert\Length(min="0")
     * @Assert\NotNull()
     */
    private $amount = 0;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
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


}
