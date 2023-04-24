<?php

namespace App\Controller\API;

use App\Controller\BaseController;
use App\Entity\Cart;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use App\Entity\Order;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations\Schema;

#[OA\Tag('Cart', "Endpoints related to the user cart")]
#[OA\Response(
    response: 401,
    ref: '#components/responses/UnauthorizedError'
)]
#[Route('/carts')]
class CartController extends BaseController
{
    #[Route('', methods: ['GET'])]
    #[OA\Get(description: 'Fetch the user cart')]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new Model(type: Cart::class, groups: ["default"])
    )]
    public function get_cart(UserRepository $userRepository): JsonResponse
    {
        return $this->json($this->get_user_entity($userRepository)->getCart());
    }

    #[Route('/{product<\d+>}', methods: ['POST'])]
    #[OA\Post(description: 'Add a product to the user cart')]
    #[OA\Parameter(
        name: "product",
        in: 'path',
        required: true,
        description: 'ID of the product to add in the cart',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        ref: '#components/responses/CartUpdateSuccess'
    )]
    #[OA\Response(
        response: 404,
        description: "Product doesn't exist",
        content: new OA\JsonContent(
            ref: '#components/schemas/GeneralError',
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
        description: "Product is already in the cart",
        content: new OA\JsonContent(
            ref: '#components/schemas/GeneralError',
            examples:
            [
                new OA\Examples(
                    example: 'already_in_cart',
                    summary: 'Product already in cart',
                    value: ['error' => 'Product with id 1 is already in the cart']
                )
            ]
        )
    )]
    public function add_product(UserRepository $userRepository, CartRepository $cartRepository, Product $product): JsonResponse
    {
        // Retrieve user entity
        $user = $this->get_user_entity($userRepository);
        // Retrieve associated cart entity
        $cart = $user->getCart();

        // Check if product is not already in the cart
        if ($cart->getProducts()->contains($product))
            return $this->error("Product with id " . $product->getId() . " is already in the cart", Response::HTTP_UNPROCESSABLE_ENTITY);

        // Add the product to the cart and save it
        $cart->addProduct($product);
        $cartRepository->save($cart, true);

        return $this->json([
            'message' => "Successfully added product with id " . $product->getId() . " in the cart",
            'cart' => $cart
        ]);
    }

    #[Route('/{product<\d+>}', methods: ['DELETE'])]
    #[OA\Delete(description: 'Remove a product from the user cart')]
    #[OA\Parameter(
        name: "product",
        in: 'path',
        required: true,
        description: 'ID of the product to remove from the cart',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        ref: '#components/responses/CartUpdateSuccess'
    )]
    #[OA\Response(
        response: 404,
        description: "Product doesn't exist",
        content: new OA\JsonContent(
            ref: '#components/schemas/GeneralError',
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
        description: "Product is not present in the cart",
        content: new OA\JsonContent(
            ref: '#components/schemas/GeneralError',
            examples:
            [
                new OA\Examples(
                    example: 'not_in_cart',
                    summary: 'Product not in cart',
                    value: ['error' => 'Product with id 1 is not in the cart']
                )
            ]
        )
    )]
    public function remove_product(UserRepository $userRepository, CartRepository $cartRepository, Product $product): JsonResponse
    {
        // Retrieve user entity
        $user = $this->get_user_entity($userRepository);
        // Retrieve associated cart entity
        $cart = $user->getCart();

        // Check if product is present in the cart
        if (!$cart->getProducts()->contains($product))
            return $this->error("Product with id " . $product->getId() . " is not in the cart", Response::HTTP_UNPROCESSABLE_ENTITY);

        // Add the product to the cart and save it
        $cart->removeProduct($product);
        $cartRepository->save($cart, true);

        return $this->json([
            'message' => "Successfully removed product with id " . $product->getId() . " from the cart",
            'cart' => $cart
        ]);
    }

    #[Route('/validate', methods: ['POST'])]
    #[OA\Post(description: 'Validate the cart into an order and clear the cart')]
    #[OA\Response(
        response: 201,
        description: "Success",
        content: new OA\JsonContent(type: 'object', properties: [
            new OA\Property(
                'message',
                type: 'string',
    default

                : "Successfully validated cart as order with id 0"
            ),
            new OA\Property(
                'order',
                ref: new Model(
                    type: Order::class,
                    groups: ["default"]
                )
            )
        ])
    )]
    #[OA\Response(
        response: 422,
        description: "Cart is empty",
        content: new OA\JsonContent(
            ref: '#components/schemas/GeneralError',
            examples:
            [
                new OA\Examples(
                    example: 'empty_cart',
                    summary: 'Empty Cart',
                    value: ['error' => 'Cannot validate empty cart']
                )
            ]
        )
    )]
    public function validate_cart(UserRepository $userRepository, CartRepository $cartRepository, OrderRepository $orderRepository): JsonResponse
    {
        // Retrieve user entity
        $user = $this->get_user_entity($userRepository);
        // Retrieve associated cart entity
        $cart = $user->getCart();

        if ($cart->getProducts()->isEmpty())
            return $this->error("Cannot validate empty cart", Response::HTTP_UNPROCESSABLE_ENTITY);

        // Create a new order with all cart products and save it
        $order = new Order();
        $order->setCreationDate(new \DateTime());
        $order->setUser($user);
        foreach ($cart->getProducts()->getIterator() as $i => $p) {
            $order->getProducts()->add($p);
        }
        $orderRepository->save($order);

        // Clear the cart and save it (+ flush)
        $cart->getProducts()->clear();
        $cartRepository->save($cart, true);

        return $this->json([
            'message' => "Successfully validated cart as order with id " . $order->getId(),
            'order' => $order
        ], Response::HTTP_CREATED);
    }
}
