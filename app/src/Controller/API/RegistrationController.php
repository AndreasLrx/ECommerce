<?php

namespace App\Controller\API;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Entity\User;
use App\Entity\Cart;
use App\Controller\BaseController;
use App\Repository\CartRepository;
use App\Repository\UserRepository;
use App\Request\RegistrationRequest;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;


#[OA\Tag('Authentication', "Endpoints related to user authentication")]
class RegistrationController extends BaseController
{
    #[Route('/register', methods: ['POST'])]
    #[Security(name: null)]
    #[OA\Post(description: 'Register a new user')]
    #[OA\RequestBody(description: "User informations", required: true, content: new Model(type: User::class, groups: ["default", "update"]))]
    #[OA\Response(
        response: 201,
        description: 'Success',
        content: new OA\JsonContent(type: "object", properties: [
            new OA\Property(
                'message',
                type: 'string',
                default: "Successfully registered as foobar"
            ),
            new OA\Property(
                'token',
                type: 'string',
                description: "Bearer Token",
                default: "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2ODE4MzA1jTYsImV4cCI6MTY4MTgzNDExNiwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidG90bzYifQ.hZsATF6Dw4hHjlOK0ijLOUn0cPaciC2Hxpr1kCbeeoSry-VDWfNNmY24PK33SGqSxOitWf3cA5LFlKu8m18gC9q3WN0KpzKyFXJNn8syx0qwn4bBzDLkEmhKqnQK0JpyqW9T3rORTyv6uayLKi_dPRJyoENy1pvFU_c7ZFPJ9tXlrYFDi0fbiSdqOgW0lNiptJ3IvLBp-Lc5ot0-22XZ8j_FHjM3OdMiY6qnSp7EGay-c6fi69D2dLiCHmcaAInVQfOaIynFkV0-ITmrkJ7u6_tjhItG6ic7iCryiy52_dDaCiPyu0zO6X-Lo8NCnZ2UOwuJ36cY7qvtILxsVhsj6w"
            )
        ])
    )]
    #[OA\Response(
        response: 422,
        description: "User informations are invalid",
        content: new OA\JsonContent(ref: '#components/schemas/GeneralError', examples:
            [
                new OA\Examples(
                    example: 'login_taken',
                    summary: "Registering with an unavailable login",
                    value: ['error' => 'Login is already taken']
                ),
                new OA\Examples(
                    example: 'invalid_mail',
                    ref: '#components/examples/UserValidationErrorExample'
                )
            ]),
    )]
    public function register(UserRepository $repository, CartRepository $cartRepository, UserPasswordHasherInterface $passwordHasher, RegistrationRequest $request, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        // Login must be unique
        if ($repository->findOneBy(["login" => $request->login]) != null)
            return $this->error("Login is already taken", Response::HTTP_UNPROCESSABLE_ENTITY);
        // Email must be unique
        else if ($repository->findOneBy(["email" => $request->email]) != null)
            return $this->error("Email is already taken", Response::HTTP_UNPROCESSABLE_ENTITY);

        $user = new User();
        $user->setLogin($request->login);
        $user->setEmail($request->email);
        $user->setFirstname($request->firstname);
        $user->setLastname($request->lastname);
        // hash the password (based on the security.yaml config for the $user class)
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $request->password
            )
        );

        $cart = new Cart();
        $cart->setUser($user);
        $cartRepository->save($cart);

        $user->setCart($cart);
        $repository->save($user, true);

        return $this->json([
            'message' => 'Successfully registered as ' . $user->getLogin(),
            'token' => $JWTManager->create($user)
        ], Response::HTTP_CREATED);
    }
}
