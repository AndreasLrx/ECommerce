<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class BaseController extends AbstractController
{

    protected function get_user_entity(UserRepository $repository): User
    {
        $user = $repository->findOneBy(['login' => $this->getUser()->getUserIdentifier()]);
        if ($user == null)
            $this->error("Unable to fetch current user data", Response::HTTP_INTERNAL_SERVER_ERROR)->send();
        return $user;
    }

    protected function error(string $error, int $status_code, array $headers = [], array $context = []): JsonResponse
    {
        return $this->json([
            'error' => $error,
        ], $status_code, $headers, $context);
    }
}
