<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends AbstractController
{
    protected function error(string $error, int $status_code, array $headers = [], array $context = []): JsonResponse
    {
        return $this->json([
            'error' => $error,
        ], $status_code, $headers, $context);
    }
}
