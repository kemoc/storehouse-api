<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Controller\REST\Product;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Kemoc\Storehouse\ApiBundle\Repository\ProductRepository;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use InvalidArgumentException;
use Kemoc\Storehouse\ApiBundle\Entity\Product;
use Kemoc\Storehouse\ApiBundle\Exception\BadRequestDataException;

/**
 * @Rest\NamePrefix("product_list_")
 * @Rest\Prefix("/product/list")
 *
 * Class ListController
 * @package Kemoc\Storehouse\ApiBundle\Controller\REST\Item
 */
class ListController extends FOSRestController
{

    /**
     * @Rest\Route(path="/")
     *
     * @return Product[]
     */
    public function getAction()
    {
        /** @var ProductRepository $productRepo */
        $productRepo = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepo->findAll();


        return $products;
    }

    /**
     * @Rest\Route(path="/withMinAmount/{minAmount}", defaults={"minAmount":5},
     *     requirements={"minAmount":"([0-9]+)"},
     *     )
     *
     */
    public function getWithMinAmountAction(int $minAmount)
    {
        if($minAmount < 0) {
            $minAmount = 0;
        }
        /** @var ProductRepository $productRepo */
        $productRepo = $this->getDoctrine()->getRepository(Product::class);
        $pager = $productRepo->findWithMinAmount($minAmount, 0, 0);

        /** @var Product[] $products */
        $products = (array)$pager->getCurrentPageResults();

        return $products;
    }

    /**
     * @Rest\Route(path="/withNoAmount/")
     *
     */
    public function getWithNoAmountAction()
    {
        /** @var ProductRepository $productRepo */
        $productRepo = $this->getDoctrine()->getRepository(Product::class);
        $pager = $productRepo->findWithAmount(0, 1, 1000);

        /** @var Product[] $products */
        $products = (array)$pager->getCurrentPageResults();

        return $products;
    }
}
