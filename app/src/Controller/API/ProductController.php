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

class ProductController extends BaseController
{
    #[Route('/products', methods: ["GET"], )]
    public function read_products(ProductRepository $repository): JsonResponse
    {
        return $this->json([$repository->findAll()]);
    }

    #[Route('/products', methods: ["POST"], )]
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

    #[Route('/products/{product<\d+>}', methods: ["GET"], )]
    public function read_product(Product $product): JsonResponse
    {
        return $this->json($product);
    }

    #[Route('/products/{product<\d+>}', methods: ["PUT"])]
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

    #[Route('/products/{product<\d+>}', name: 'app_products', methods: ["DELETE"])]
    public function delete_product(ProductRepository $repository, Product $product): JsonResponse
    {
        $repository->remove($product, true);
        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}