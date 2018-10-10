<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Controller\REST\Item;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Kemoc\Storehouse\ApiBundle\Repository\ItemRepository;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use InvalidArgumentException;
use Kemoc\Storehouse\ApiBundle\Entity\Item;
use Kemoc\Storehouse\ApiBundle\Exception\BadRequestDataException;

/**
 * @Rest\NamePrefix("item_list_")
 * @Rest\Prefix("/item/list")
 *
 * Class ListController
 * @package Kemoc\Storehouse\ApiBundle\Controller\REST\Item
 */
class ListController extends FOSRestController
{

    /**
     * @Rest\Route(path="/")
     *
     * @return Item[]
     */
    public function getAction()
    {
        /** @var ItemRepository $itemRepo */
        $itemRepo = $this->getDoctrine()->getRepository(Item::class);
        $items = $itemRepo->findAll();


        return $items;
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
        /** @var ItemRepository $itemRepo */
        $itemRepo = $this->getDoctrine()->getRepository(Item::class);
        $pager = $itemRepo->findWithMinAmount($minAmount, 0, 0);

        /** @var Item[] $items */
        $items = (array)$pager->getCurrentPageResults();

        return $items;
    }

    /**
     * @Rest\Route(path="/withNoAmount/")
     *
     */
    public function getWithNoAmountAction()
    {
        /** @var ItemRepository $itemRepo */
        $itemRepo = $this->getDoctrine()->getRepository(Item::class);
        $pager = $itemRepo->findWithAmount(0, 1, 1000);

        /** @var Item[] $items */
        $items = (array)$pager->getCurrentPageResults();

        return $items;
    }
}
