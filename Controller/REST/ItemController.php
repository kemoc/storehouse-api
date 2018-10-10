<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Controller\REST;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
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
 * @Rest\NamePrefix("item_")
 * @Rest\Prefix("/item")
 *
 * Class ItemController
 * @package Kemoc\Storehouse\ApiBundle\Controller\REST
 *
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
     * @Rest\Route(path="/{id}", requirements={"id":"[1-9]+[0-9]*"})
     * @ParamConverter("item", options={"mapping": {"id": "id"}})
     *
     * @Operation(
     *     tags={""},
     *     summary="Gets a Item for a given id",
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Item id",
     *         required=true,
     *         type="integer",
     *         schema=""
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when the page is not found"
     *     )
     * )
     *
     *
     * @param int $id
     *
     * @return Item
     *
     * @throws NotFoundHttpException
     *
     * curl -G storehouse-api.local/storehouse/api/rest/item/1
     *
     */
    public function getAction(Item $item): Item
    {

        return $item;
    }

    /**
     * Create a Type from the submitted data.
     *
     * @Rest\Route(path="/")
     * @Operation(
     *     tags={""},
     *     summary="Creates a new type from the submitted data.",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="amount",
     *         in="formData",
     *         description="",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when the form has errors"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Returned when not authenticated"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when not having permissions"
     *     )
     * )
     *
     *
     * @param Request $request the request object
     * @throws BadRequestDataException
     *
     * @return FormInterface|View
     */
    public function postAction(Request $request) {
        try {
            try {

                $persistedItem = $this->createNewItem($request);

                $routeOptions = [
                    'id' => $persistedItem->getId()
                ];

                /*return $this->routeRedirectView('storehouse_api_rest_item_get', $routeOptions,
                    Response::HTTP_CREATED
                );*/

                return View::create($persistedItem, Response::HTTP_CREATED);

            } catch (InvalidFormDataException $exception) {

                return $exception->getForm();
            }
        } catch (Exception $exception) {
            throw $this->createFosRestSupportedException($exception);
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

    protected function createFosRestSupportedException(Throwable $exception) {

        return new BadRequestDataException($exception->getMessage(), 0, $exception);
    }

    /**
     * Update existing type from the submitted data or create a new type.
     * All required fields must be set within request data.
     *
     * @Rest\Route(path="/{id}", requirements={"id":"[1-9]+[0-9]*"})
     * @Operation(
     *     tags={""},
     *     summary="Update existing type from the submitted data or create a new type.",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="",
     *         required=true,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="amount",
     *         in="body",
     *         description="",
     *         required=true,
     *         type="integer",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="",
     *         required=false,
     *         type="integer",
     *         schema=""
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned when the Type is created"
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when the form has errors"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Returned when not authenticated"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when not having permissions"
     *     )
     * )
     *
     *
     * @param Request $request
     * @param int $id
     *
     * @return FormInterface|View
     *
     * @throws NotFoundHttpException when Item not exist
     * @throws BadRequestDataException
     */
    public function putAction(Request $request, int $id = null) {
        try {
            try {

                /** @var ItemRepository $itemRepository */
                $itemRepository = $this->getDoctrine()->getRepository(Item::class);
                $item = null;
                if($id) {
                    $item = $itemRepository->find($id);
                    if (!$item) {
                        throw new NotFoundHttpException(sprintf('Not found Item of id = "%s"', $id));
                    }
                }
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

                return $this->routeRedirectView('storehouse_api_rest_item_get', $routeOptions, $statusCode);

            } catch (InvalidFormDataException $exception) {

                return $exception->getForm();
            }
        } catch (Exception $exception) {
            throw $this->createFosRestSupportedException($exception);
        }
    }

    /**
     * REST action which deletes type by id.
     * Method: DELETE, url: /storehouse/api/rest/item/{id}.{_format}
     *
     * @Rest\Route(path="/{id}", requirements={"id":"[1-9]+[0-9]*"})
     * @Operation(
     *     tags={""},
     *     summary="Deletes an Item for a given id",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Returned when not authenticated"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when not having permissions"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when the type is not found"
     *     )
     * )
     *
     *
     * @param Request $request
     * @param int $id
     *
     * @return void
     *
     * @throws BadRequestDataException
     */
    public function deleteAction(Request $request, Item $item) {

        try {
            $this->getDoctrine()->getManager()->remove($item);
            $this->getDoctrine()->getManager()->flush();

        } catch (Exception $exception) {
            throw $this->createFosRestSupportedException($exception);
        }
    }

    /**
     * Update existing Item from the submitted data.
     *
     * @Rest\Route(path="/{id}", requirements={"id":"[\d]+"})
     * @Operation(
     *     tags={""},
     *     summary="Update existing type from the submitted data.",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="",
     *         required=true,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="amount",
     *         in="body",
     *         description="",
     *         required=true,
     *         type="integer",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="",
     *         required=true,
     *         type="integer",
     *         schema=""
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when the form has errors"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Returned when not authenticated"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when not having permissions"
     *     )
     * )
     *
     *
     * @param Request $request the request object
     * @param int $id of the Item
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when type does not exist
     * @throws BadRequestDataException
     */
    public function patchAction(Request $request, int $id) {
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

                //return $this->routeRedirectView('storehouse_api_rest_item_get', $routeOptions, $statusCode);
                return View::create($item, $statusCode);

            } catch (InvalidFormDataException $exception) {

                return $exception->getForm();
            }
        } catch (Exception $exception) {
            throw $this->createFosRestSupportedException($exception);
        }
    }
}
