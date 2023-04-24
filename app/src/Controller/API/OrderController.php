<?php

namespace App\Controller\API;

use App\Controller\BaseController;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

#[OA\Tag('Orders', "Endpoints related to the user orders")]
#[Route('/orders')]
class OrderController extends BaseController
{
    #[Route('', methods: ['GET'])]
    #[OA\Get(description: 'Fetch the user orders')]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Order::class, groups: ["default"])))
    )]
    public function get_user_orders(UserRepository $userRepository): JsonResponse
    {
        return $this->json($this->get_user_entity($userRepository)->getOrders()->toArray());
    }

    #[Route('/{order<\d+>}', methods: ['GET'])]
    #[OA\Get(description: 'Fetch a specific user order')]
    #[OA\Parameter(
        name: "order",
        in: 'path',
        required: true,
        description: 'ID of the order to fetch',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new Model(type: Order::class, groups: ["default"])
    )]
    #[OA\Response(
        response: 401,
        description: "Order doesn't belong to the authentified user",
        content: new OA\JsonContent(
            ref: '#components/schemas/GeneralError',
            examples:
            [
                new OA\Examples(
                    example: 'insufficient_permission',
                    summary: 'Insufficient permission',
                    value: ['error' => 'You can only see your orders']
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Order doesn't exist",
        content: new OA\JsonContent(
            ref: '#components/schemas/GeneralError',
            examples:
            [
                new OA\Examples(
                    example: 'not_found',
                    ref: '#components/examples/OrderNotFoundErrorExample'
                )
            ]
        )
    )]
    public function get_user_order(UserRepository $userRepository, Order $order): JsonResponse
    {
        if ($this->get_user_entity($userRepository) != $order->getUser())
            return $this->error("You can only see your orders", Response::HTTP_FORBIDDEN);

        return $this->json($order);
    }

}
