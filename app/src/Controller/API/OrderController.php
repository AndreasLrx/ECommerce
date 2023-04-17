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
        $user = $this->get_user_entity($userRepository);
        if ($user == null)
            return $this->error("Unable to fetch current user data", Response::HTTP_INTERNAL_SERVER_ERROR);

        return $this->json($user->getOrders()->toArray());
    }

    #[Route('/{order<\d+>}', methods: ['GET'])]
    public function get_user_order(UserRepository $userRepository, Order $order): JsonResponse
    {
        $user = $this->get_user_entity($userRepository);
        if ($user == null)
            return $this->error("Unable to fetch current user data", Response::HTTP_INTERNAL_SERVER_ERROR);

        if ($user != $order->getUser())
            return $this->error("You can only see your orders", Response::HTTP_FORBIDDEN);

        return $this->json($order);
    }

}
