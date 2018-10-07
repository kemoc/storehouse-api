<?php
declare(strict_types=1);



namespace Kemoc\Storehouse\ApiBundle\Controller\REST;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
//use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use InvalidArgumentException;
use Kemoc\Storehouse\ApiBundle\Entity\Item;
use Kemoc\Storehouse\ApiBundle\Exception\BadRequestDataException;
use Kemoc\Storehouse\ApiBundle\Exception\InvalidFormDataException;
use Kemoc\Storehouse\ApiBundle\Form\Factory\Item\FormFactory;
use Kemoc\Storehouse\ApiBundle\Repository\ItemRepository;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Kemoc\Storehouse\ApiBundle\Form\Data\Item\Create as CreateItemData;
use Kemoc\Storehouse\ApiBundle\Form\Data\Item\Update as UpdateItemData;
use Throwable;
use Exception;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormInterface;

/**
 * Class ItemController
 * @package Kemoc\Storehouse\ApiBundle\Controller\REST
 *
 * @Rest\Route(name="api_rest_item", path="/item")
 */
class ItemController extends FOSRestController
{
    /**
     * @var FormFactory
     */
    protected $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getFormFactory()
    {

    	return $this->formFactory;
    }
    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Type for a given id",
     *   output = "Kemoc\Storehouse\ApiBundle\Entity\Item",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the page is not found"
     *   }
     * )
     *
     * @param int $id
     *
     * @return Item
     *
     * @throws NotFoundHttpException
     * curl -G storehouse-api.local/storehouse/api/rest/item/1
     * "nelmio/api-doc-bundle": "^2.0",
     * nelmio/api-doc-bundle ^2.0
     */
    public function getItemAction(int $id): Item
    {
        $item = null;
        /** @var ItemRepository $itemsRepo */
        $itemsRepo = $this->getDoctrine()->getRepository(Item::class);
        try {
            $item = $itemsRepo->find($id);
        } catch (\Exception $exception) {
            $item = null;
        }

        if (!$item) {
            throw new NotFoundHttpException(sprintf('The Item resource where id = "%s" was not found.', $id));
        }

        return $item;
    }

    /**
     * Create a Type from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new type from the submitted data.",
     *   input = "Kemoc\Storehouse\ApiBundle\Entity\Item",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors",
     *     401 = "Returned when not authenticated",
     *     403 = "Returned when not having permissions"
     *   }
     * )
     *
     * @param Request $request the request object
     * @throws BadRequestDataException
     *
     * @return FormInterface|View
     */
    public function postItemAction(Request $request) {
        try {
            try {

                $persistedItem = $this->createNewItem($request);

                $routeOptions = [
                    'id' => $persistedItem->getId(),
                    '_format' => $request->get('_format')
                ];

                return $this->routeRedirectView('api_rest_item_get_item', $routeOptions,
                    Response::HTTP_CREATED
                );

            } catch (InvalidFormDataException $exception) {

                return $exception->getForm();
            }
        } catch (Exception $exception) {
            throw $this->getFosRestSupportedException($exception);
        }
    }

    /**
     * Creates new type from request parameters and persists it.
     *
     */
    protected function createNewItem(Request $request): Item
    {
        $item = new Item();
        $parameters = $request->request->all();
        $persistedItem = $this->processForm($item, $parameters, Request::METHOD_POST);

        return $persistedItem;
    }

    /**
     * Processes the form.
     *
     * @param Item $item
     * @param array $parameters
     * @param String $method
     * @return Item
     *
     * @throws InvalidFormDataException
     * @throws InvalidArgumentException
     */
    private function processForm(Item $item, array $parameters, $method = Request::METHOD_PUT) {
        if($method === Request::METHOD_POST) {
            $createData = new CreateItemData($item);
            $form = $this->getFormFactory()->getCreate($createData, $method);
        }
        elseif ($method === Request::METHOD_PUT) {
            if($item->getId()) {
                $updateData = new UpdateItemData($item);
                $form = $this->getFormFactory()->getUpdate($updateData, $method);
            }
            else {
                $createData = new CreateItemData($item);
                $form = $this->getFormFactory()->getCreate($createData, $method);
            }
        }
        elseif($method === Request::METHOD_PATCH) {
            $updateData = new UpdateItemData($item);
            $form = $this->getFormFactory()->getUpdate($updateData);
        }
        else {
            throw new InvalidArgumentException(sprintf("Unsupported method: '%s'", $method));
        }
        //$form = $this->createForm(new TypeType(), $item, ['method' => $method]);

        $form->submit($parameters, Request::METHOD_PATCH !== $method);

        if ($form->isSubmitted() and $form->isValid()) {
            /** @var CreateItemData|UpdateItemData $data */
            $data = $form->getData();
            $item = $data->getItemEntity();
            $data->copyTo($item);
            ///** @var ItemRepository $itemRepository */
            //$itemRepository = $this->getDoctrine()->getRepository(Item::class);
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $item;
        }

        throw new InvalidFormDataException($form, 'Invalid submitted form data');
    }

    protected function getFosRestSupportedException(Throwable $exception) {

        return new BadRequestDataException($exception->getMessage(), 0, $exception);
    }

    /**
     * Update existing type from the submitted data or create a new type.
     * All required fields must be set within request data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Kemoc\Storehouse\ApiBundle\Entity\Item",
     *   statusCodes = {
     *     201 = "Returned when the Type is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors",
     *     401 = "Returned when not authenticated",
     *     403 = "Returned when not having permissions"
     *   }
     * )
     *
     * @param Request $request
     * @param int $id
     *
     * @return FormInterface|View
     *
     * @throws NotFoundHttpException when Item not exist
     * @throws BadRequestDataException
     */
    public function putItemAction(Request $request, int $id) {
        try {
            try {

                /** @var ItemRepository $itemRepository */
                $itemRepository = $this->getDoctrine()->getRepository(Item::class);
                $item = $itemRepository->find($id);
                if (!$item) {
                    $statusCode = Response::HTTP_CREATED;
                    $item = $this->createNewItem($request);
                } else {
                    $statusCode = Response::HTTP_NO_CONTENT;
                    $item = $this->processForm($item, $request->request->all(), Request::METHOD_PUT);
                }

                $routeOptions = [
                    'id' => $item->getId(),
                    '_format' => $request->get('_format')
                ];

                return $this->routeRedirectView('api_rest_item_get_item', $routeOptions, $statusCode);

            } catch (InvalidFormDataException $exception) {

                return $exception->getForm();
            }
        } catch (Exception $exception) {
            throw $this->getFosRestSupportedException($exception);
        }
    }

    /**
     * REST action which deletes type by id.
     * Method: DELETE, url: /api/typoscript/types/{id}.{_format}
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Deletes a Type for a given id",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     401 = "Returned when not authenticated",
     *     403 = "Returned when not having permissions",
     *     404 = "Returned when the type is not found"
     *   }
     * )
     *
     * @param Request $request
     * @param int $id
     *
     * @return void
     *
     * @throws BadRequestDataException
     */
    public function deleteItemAction(Request $request, int $id) {

        /** @var ItemRepository $itemRepository */
        $itemRepository = $this->getDoctrine()->getRepository(Item::class);
        $item = $itemRepository->find($id);

        if ($item) {
            try {
                $this->getDoctrine()->getManager()->remove($item);
                $this->getDoctrine()->getManager()->flush();
            } catch (Exception $exception) {
                throw $this->getFosRestSupportedException($exception);
            }
        } else {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }
    }

    /**
     * Update existing type from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Kemoc\Storehouse\ApiBundle\Entity\Item",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors",
     *     401 = "Returned when not authenticated",
     *     403 = "Returned when not having permissions"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int $id of the Item
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when type does not exist
     * @throws BadRequestDataException
     */
    public function patchItemAction(Request $request, int $id) {
        try {
            try {
                /** @var ItemRepository $itemRepository */
                $itemRepository = $this->getDoctrine()->getRepository(Item::class);
                $item = $itemRepository->find($id);
                if (!$item) {
                    throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
                }
                $statusCode = Response::HTTP_NO_CONTENT;
                $item = $this->processForm($item, $request->request->all(), Request::METHOD_PATCH);

                $routeOptions = [
                    'id' => $item->getId(),
                    '_format' => $request->get('_format')
                ];

                return $this->routeRedirectView('api_rest_item_get_item', $routeOptions, $statusCode);

            } catch (InvalidFormDataException $exception) {

                return $exception->getForm();
            }
        } catch (Exception $exception) {
            throw $this->getFosRestSupportedException($exception);
        }
    }
}
