<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ErrorController extends AbstractController
{
    public function show(FlattenException $exception, DebugLoggerInterface $logger = null, KernelInterface $kernel): JsonResponse
    {
        $res = ["error" => $exception->getStatusText()];
        if ($kernel->getEnvironment() == "dev")
            $res["details"] = $exception->getMessage();

        return $this->json($res, $exception->getStatusCode());
    }
}
