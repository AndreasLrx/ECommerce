<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\UserRepository;
use App\Entity\User;

class BaseController extends AbstractController
{

    protected function get_user_entity(UserRepository $repository): User|null
    {
        return $repository->findOneBy(['login' => $this->getUser()->getUserIdentifier()]);
    }

    protected function error(string $error, int $status_code, array $headers = [], array $context = []): JsonResponse
    {
        return $this->json([
            'error' => $error,
        ], $status_code, $headers, $context);
    }
}
