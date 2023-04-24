<?php

namespace App\Controller\API;

use App\Controller\BaseController;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Request\ProductRequest;
use App\Request\ProductUpdateRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;


#[OA\Tag('Products', "Endpoints related to the shop products")]
#[Route('/products')]
class ProductController extends BaseController
{
    #[Route('', methods: ["GET"], )]
    #[OA\Get(description: 'Fetch the available products')]
    #[Security(name: null)]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Product::class, groups: ["default"])))
    )]
    public function read_products(ProductRepository $repository): JsonResponse
    {
        return $this->json([$repository->findAll()]);
    }

    #[Route('', methods: ["POST"])]
    #[OA\Post(description: 'Create a new product')]
    #[OA\RequestBody(description: "Product to create", required: true, content: new Model(type: Product::class, groups: ["create"]))]
    #[OA\Response(
        response: 201,
        ref: '#components/responses/ProductUpdateSuccess'
    )]
    #[OA\Response(
        response: 401,
        ref: '#components/responses/UnauthorizedError'
    )]
    #[OA\Response(
        response: 422,
        description: "Product parameters are invalid",
        content: new OA\JsonContent(
            ref: '#components/schemas/GeneralError',
            examples:
            [
                new OA\Examples(
                    example: 'invalid_price',
                    ref: '#components/examples/ProductValidationErrorExamples'
                )
            ]
        )
    )]
    public function create_product(ProductRepository $repository, ProductRequest $request): JsonResponse
    {
        $product = new Product();
        $product->setName($request->name);
        $product->setDescription($request->description);
        $product->setPhoto($request->photo);
        $product->setPrice($request->price);
        $repository->save($product, true);

        return $this->json([
            'message' => 'Successfully created new product',
            'product' => $product
        ], Response::HTTP_CREATED);
    }

    #[Route('/{product<\d+>}', methods: ["GET"], )]
    #[OA\Get(description: 'Fetch a specific product')]
    #[Security(name: null)]
    #[OA\Parameter(
        name: "product",
        in: 'path',
        required: true,
        description: 'ID of the product to fetch',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new Model(type: Product::class, groups: ["default"])
    )]
    #[OA\Response(
        response: 404,
        description: "Product doesn't exist",
        content: new OA\JsonContent(
            ref: '#components/schemas/BasicError',
            examples:
            [
                new OA\Examples(
                    example: 'not_found',
                    ref: '#components/examples/ProductNotFoundErrorExample'
                )
            ]
        )
    )]
    public function read_product(Product $product): JsonResponse
    {
        return $this->json($product);
    }

    #[Route('/{product<\d+>}', methods: ["PUT"])]
    #[OA\Put(description: 'Update an existing product')]
    #[OA\RequestBody(description: "Product to update", required: true, content: new Model(type: Product::class, groups: ["create"]))]
    #[OA\Response(
        response: 200,
        ref: '#components/responses/ProductUpdateSuccess'
    )]
    #[OA\Response(
        response: 401,
        ref: '#components/responses/UnauthorizedError'
    )]
    #[OA\Response(
        response: 404,
        description: "Product doesn't exist",
        content: new OA\JsonContent(
            ref: '#components/schemas/BasicError',
            examples:
            [
                new OA\Examples(
                    example: 'not_found',
                    ref: '#components/examples/ProductNotFoundErrorExample'
                )
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Product parameters are invalid",
        content: new OA\JsonContent(
            ref: '#components/schemas/GeneralError',
            examples:
            [
                new OA\Examples(
                    example: 'invalid_price',
                    ref: '#components/examples/ProductValidationErrorExamples'
                )
            ]
        )
    )]
    public function update_product(ProductRepository $repository, ProductUpdateRequest $request, Product $product): JsonResponse
    {
        if ($request->name != null)
            $product->setName($request->name);
        if ($request->description != null)
            $product->setDescription($request->description);
        if ($request->photo != null)
            $product->setPhoto($request->photo);
        if ($request->price != null)
            $product->setPrice($request->price);

        $repository->save($product, true);


        return $this->json([
            'message' => 'Successfully updated product with id ' . $product->getId(),
            'product' => $product
        ]);
    }

    #[Route('/{product<\d+>}', name: 'app_products', methods: ["DELETE"])]
    #[OA\Delete(description: 'Delete an existing product')]
    #[OA\Response(
        response: 204,
        description: "Success"
    )]
    #[OA\Response(
        response: 401,
        ref: '#components/responses/UnauthorizedError'
    )]
    #[OA\Response(
        response: 404,
        description: "Product doesn't exist",
        content: new OA\JsonContent(
            ref: '#components/schemas/BasicError',
            examples:
            [
                new OA\Examples(
                    example: 'not_found',
                    ref: '#components/examples/ProductNotFoundErrorExample'
                )
            ]
        )
    )]
    public function delete_product(ProductRepository $repository, Product $product): JsonResponse
    {
        $repository->remove($product, true);
        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
