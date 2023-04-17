<?php

namespace App\Controller\API;

use App\Controller\BaseController;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;
use Symfony\Component\HttpFoundation\Response;

#[Route('/orders')]
class OrderController extends BaseController
{
    #[Route('/', methods: ['GET'])]
    public function get_user_orders(UserRepository $userRepository): JsonResponse
    {
        return $this->json($this->get_user_entity($userRepository)->getOrders()->toArray());
    }

    #[Route('/{order<\d+>}', methods: ['GET'])]
    public function get_user_order(UserRepository $userRepository, Order $order): JsonResponse
    {
        if ($this->get_user_entity($userRepository) != $order->getUser())
            return $this->error("You can only see your orders", Response::HTTP_FORBIDDEN);

        return $this->json($order);
    }

}
