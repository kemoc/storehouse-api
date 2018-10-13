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
use Kemoc\Storehouse\ApiBundle\Entity\Product;


class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param int $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return Product|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        /** @var Product|null $res */
        $res = parent::find((int)$id, $lockMode, $lockVersion);


        return $res;
    }

    public function findWithMinAmount(int $minAmount = 5, int $page = 0, int $maxPerPage = Product::NUM_ITEMS)
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.amount > :amount')
            ->setParameter('amount', $minAmount - 1, Type::INTEGER)
            ->addOrderBy('p.id', Criteria::ASC)
        ;

        return $this->createPaginator($qb->getQuery(), $page, $maxPerPage);
    }

    public function findWithAmount(int $amount = 0, int $page = 0, int $maxPerPage = Product::NUM_ITEMS)
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.amount = :amount')
            ->setParameter('amount', $amount, Type::INTEGER)
            ->addOrderBy('p.id', Criteria::ASC)
        ;

        return $this->createPaginator($qb->getQuery(), $page, $maxPerPage);
    }

    private function createPaginator(Query $query, int $page, int $maxPerPage = Product::NUM_ITEMS): Pagerfanta
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
