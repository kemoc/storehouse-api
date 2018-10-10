<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Doctrine\Common\Collections\Criteria;
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

    public function findWithMinAmount(int $minAmount = 5, int $page = 0, int $maxPerPage = Item::NUM_ITEMS)
    {
        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.amount > :amount')
            ->setParameter('amount', $minAmount - 1, Type::INTEGER)
            ->addOrderBy('i.id', Criteria::ASC)
        ;

        return $this->createPaginator($qb->getQuery(), $page, $maxPerPage);
    }

    public function findWithAmount(int $amount = 0, int $page = 0, int $maxPerPage = Item::NUM_ITEMS)
    {
        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.amount = :amount')
            ->setParameter('amount', $amount, Type::INTEGER)
            ->addOrderBy('i.id', Criteria::ASC)
        ;

        return $this->createPaginator($qb->getQuery(), $page, $maxPerPage);
    }

    private function createPaginator(Query $query, int $page, int $maxPerPage = Item::NUM_ITEMS): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        if($maxPerPage > 0) {
            $paginator->setMaxPerPage($maxPerPage);
        }
        if($page > 0) {
            $paginator->setCurrentPage($page);
        }

        return $paginator;
    }
}
