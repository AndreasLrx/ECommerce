<?php

namespace App\Controller\API;

use App\Controller\BaseController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use App\Entity\Order;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use OpenApi\Attributes as OA;

#[OA\Tag('Cart', "Endpoints related to the user cart")]
#[Route('/carts')]
class CartController extends BaseController
{
    #[Route('/', methods: ['GET'])]
    public function get_cart(UserRepository $userRepository): JsonResponse
    {
        return $this->json($this->get_user_entity($userRepository)->getCart());
    }

    #[Route('/{product<\d+>}', methods: ['POST'])]
    public function add_product(UserRepository $userRepository, CartRepository $cartRepository, Product $product): JsonResponse
    {
        // Retrieve user entity
        $user = $this->get_user_entity($userRepository);
        // Retrieve associated cart entity
        $cart = $user->getCart();

        // Check if product is not already in the cart
        if ($cart->getProducts()->contains($product))
            return $this->error("Product with id " . $product->getId() . " is already in the cart", Response::HTTP_BAD_REQUEST);

        // Add the product to the cart and save it
        $cart->addProduct($product);
        $cartRepository->save($cart, true);

        return $this->json([
            'message' => "Successfully added product with id " . $product->getId() . " to cart",
            'cart' => $cart
        ]);
    }

    #[Route('/{product<\d+>}', methods: ['DELETE'])]
    public function remove_product(UserRepository $userRepository, CartRepository $cartRepository, Product $product): JsonResponse
    {
        // Retrieve user entity
        $user = $this->get_user_entity($userRepository);
        // Retrieve associated cart entity
        $cart = $user->getCart();

        // Check if product is present in the cart
        if (!$cart->getProducts()->contains($product))
            return $this->error("Product with id " . $product->getId() . " is not in the cart", Response::HTTP_BAD_REQUEST);

        // Add the product to the cart and save it
        $cart->removeProduct($product);
        $cartRepository->save($cart, true);

        return $this->json([
            'message' => "Successfully removed product with id " . $product->getId() . " from cart",
            'cart' => $cart
        ]);
    }

    #[Route('/validate', methods: ['POST'])]
    public function validate_cart(UserRepository $userRepository, CartRepository $cartRepository, OrderRepository $orderRepository): JsonResponse
    {
        // Retrieve user entity
        $user = $this->get_user_entity($userRepository);
        // Retrieve associated cart entity
        $cart = $user->getCart();

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
