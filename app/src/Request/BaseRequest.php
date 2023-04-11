<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest
{
    public function __construct(protected ValidatorInterface $validator, protected KernelInterface $kernel)
    {
        $this->populate();

        if ($this->autoValidateRequest()) {
            $this->validate();
        }
    }

    public function validate()
    {
        $errors = $this->validator->validate($this);
        if (count($errors) == 0)
            return;

        $res = ['error' => 'Parameters validation failed'];

        if ($this->kernel->getEnvironment() == "dev") {
            $res['errors'] = [];
            /** @var \Symfony\Component\Validator\ConstraintViolation  */
            foreach ($errors as $message) {
                $res['errors'][] = [
                    'property' => $message->getPropertyPath(),
                    'value' => $message->getInvalidValue(),
                    'message' => $message->getMessage(),
                ];
            }

        }

        $response = new JsonResponse($res, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->send();
    }

    public function getRequest(): Request
    {
        return Request::createFromGlobals();
    }

    protected function populate(): void
    {
        foreach ($this->getRequest()->toArray() as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}
