<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Kemoc\Storehouse\ApiBundle\Entity\Item;


class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * @param int $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return Item|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        /** @var Item|null $res */
        $res = parent::find($id, $lockMode, $lockVersion);


        return $res;
    }
}
