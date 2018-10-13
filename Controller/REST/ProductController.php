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
use Kemoc\Storehouse\ApiBundle\Entity\Product;
use Kemoc\Storehouse\ApiBundle\Exception\BadRequestDataException;
use Kemoc\Storehouse\ApiBundle\Exception\InvalidFormDataException;
use Kemoc\Storehouse\ApiBundle\Form\Factory\Product\FormFactory;
use Kemoc\Storehouse\ApiBundle\Repository\ProductRepository;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Kemoc\Storehouse\ApiBundle\Form\Data\Product\Create as CreateProductData;
use Kemoc\Storehouse\ApiBundle\Form\Data\Product\Update as UpdateProductData;
use Throwable;
use Exception;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormInterface;


/**
 * @Rest\NamePrefix("product_")
 * @Rest\Prefix("/product")
 *
 * Class ItemController
 * @package Kemoc\Storehouse\ApiBundle\Controller\REST
 *
 */
class ProductController extends FOSRestController
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
     * @ParamConverter("product", options={"mapping": {"id": "id"}})
     *
     *
     * @param Product $product
     *
     * @return Product
     *
     * @throws NotFoundHttpException
     *
     * curl -G storehouse-api.local/storehouse/api/rest/product/1
     *
     */
    public function getAction(Product $product): Product
    {

        return $product;
    }

    /**
     * Create a Product from the submitted data.
     *
     * @Rest\Route(path="/")
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
                $parameters = $request->request->all();
                $product = new Product();

                $createData = new CreateProductData($product);
                $form = $this->getFormFactory()->getCreate($createData, false, Request::METHOD_POST);
                $form->submit($parameters, true);

                if ($form->isSubmitted() and $form->isValid()) {
                    /** @var CreateProductData $data */
                    $data = $form->getData();
                    $product = $data->getProductEntity();
                    $data->copyTo($product);

                    // @todo: add: create before event
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($product);
                    $em->flush();

                    // @todo: add: created event
                }
                else {

                    throw new InvalidFormDataException($form, 'Invalid submitted form data');
                }

                $routeOptions = [
                    'id' => $product->getId()
                ];

                /*return $this->routeRedirectView('storehouse_api_rest_item_get', $routeOptions,
                    Response::HTTP_CREATED
                );*/

                return View::create($product, Response::HTTP_CREATED);

            } catch (InvalidFormDataException $exception) {

                return $exception->getForm();
            }
        } catch (Throwable $exception) {
            throw $this->createFosRestSupportedException($exception);
        }
    }

    protected function createFosRestSupportedException(Throwable $exception) {

        return new BadRequestDataException($exception->getMessage(), 0, $exception);
    }

    /**
     * Update existing Product from the submitted data or create a new Product.
     * All required fields must be set within request data.
     *
     * @Rest\Route(path="/{id}", requirements={"id":"[1-9]+[0-9]*"})
     *
     * @param Request $request
     * @param int $id
     *
     * @return FormInterface|View
     *
     * @throws NotFoundHttpException when Product not exist
     * @throws BadRequestDataException
     */
    public function putAction(Request $request, int $id = null) {
        try {
            try {

                /** @var ProductRepository $productRepository */
                $productRepository = $this->getDoctrine()->getRepository(Product::class);
                $product = null;
                if ($id) {
                    $product = $productRepository->find($id);
                    if (!$product) {
                        throw new NotFoundHttpException(sprintf('Not found Product of id = "%s"', $id));
                    }
                }

                $parameters = $request->request->all();
                if (!$product) {
                    $statusCode = Response::HTTP_CREATED;
                    $product = new Product();

                    $createData = new CreateProductData($product);
                    $form = $this->getFormFactory()->getCreate($createData, false, Request::METHOD_POST);
                    $form->submit($parameters, true);

                    if ($form->isSubmitted() and $form->isValid()) {
                        /** @var CreateProductData $data */
                        $data = $form->getData();
                        $product = $data->getProductEntity();
                        $data->copyTo($product);

                        // @todo: add: create before event
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($product);
                        $em->flush();

                        // @todo: add: created event
                    }
                    else {

                        throw new InvalidFormDataException($form, 'Invalid submitted form data');
                    }
                }
                else {
                    $statusCode = Response::HTTP_NO_CONTENT;
                    //$item = $this->processForm($item, $request->request->all(), Request::METHOD_PUT);
                    $updateData = new UpdateProductData($product);
                    $form = $this->getFormFactory()->getUpdate($updateData, false, Request::METHOD_PUT);
                    $form->submit($parameters, true);

                    if ($form->isSubmitted() and $form->isValid()) {
                        /** @var UpdateProductData $data */
                        $data = $form->getData();
                        $product = $data->getProductEntity();
                        $data->copyTo($product);

                        // @todo: add: update before event
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($product);
                        $em->flush();

                        // @todo: add: updated event
                    }
                    else {

                        throw new InvalidFormDataException($form, 'Invalid submitted form data');
                    }
                }

                $routeOptions = [
                    'id' => $product->getId(),
                    '_format' => $request->get('_format')
                ];

                //return $this->routeRedirectView('storehouse_api_rest_item_get', $routeOptions, $statusCode);
                return View::create($product, $statusCode);

            } catch (InvalidFormDataException $exception) {

                return $exception->getForm();
            }
        } catch (NotFoundHttpException $nfe) {

            throw $nfe;
        } catch (Exception $exception) {

            throw $this->createFosRestSupportedException($exception);
        }
    }

    /**
     * REST action which deletes Product by id.
     * Method: DELETE, url: /storehouse/api/rest/product/{id}.{_format}
     *
     * @Rest\Route(path="/{id}", requirements={"id":"[1-9]+[0-9]*"})
     *
     * @param Request $request
     * @param Product $product
     *
     * @return void
     *
     * @throws BadRequestDataException
     */
    public function deleteAction(Request $request, Product $product) {

        try {
            // @todo: add: delete before event
            $this->getDoctrine()->getManager()->remove($product);
            $this->getDoctrine()->getManager()->flush();

            // @todo: add: deleted event

        } catch (Exception $exception) {
            throw $this->createFosRestSupportedException($exception);
        }
    }

    /**
     * Update existing Product from the submitted data.
     *
     * @Rest\Route(path="/{id}", requirements={"id":"[\d]+"})
     *
     * @param Request $request the request object
     * @param int $id of the Product
     *
     * @return FormInterface|View
     *
     * @throws NotFoundHttpException when Product does not exist
     * @throws BadRequestDataException
     */
    public function patchAction(Request $request, int $id) {
        try {
            try {
                /** @var ProductRepository $productRepository */
                $productRepository = $this->getDoctrine()->getRepository(Product::class);
                $product = $productRepository->find($id);
                if (!$product) {
                    throw new NotFoundHttpException(sprintf('The resource Product of id = "%s" was not found.', $id));
                }
                $statusCode = Response::HTTP_NO_CONTENT;
                $parameters = $request->request->all();

                $updateData = new UpdateProductData($product);
                $form = $this->getFormFactory()->getUpdate($updateData, false, Request::METHOD_PATCH);
                $form->submit($parameters, false);

                if ($form->isSubmitted() and $form->isValid()) {
                    /** @var UpdateProductData $data */
                    $data = $form->getData();
                    $product = $data->getProductEntity();
                    $data->copyTo($product);

                    // @todo: add: update before event
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($product);
                    $em->flush();

                    // @todo: add: updated event
                }
                else {

                    throw new InvalidFormDataException($form, 'Invalid submitted form data');
                }

                $routeOptions = [
                    'id' => $product->getId(),
                    '_format' => $request->get('_format')
                ];

                //return $this->routeRedirectView('storehouse_api_rest_item_get', $routeOptions, $statusCode);
                return View::create($product, $statusCode);

            } catch (InvalidFormDataException $exception) {

                return $exception->getForm();
            }
        } catch (NotFoundHttpException $nfe) {

            throw $nfe;
        } catch (Throwable $exception) {

            throw $this->createFosRestSupportedException($exception);
        }
    }
}
