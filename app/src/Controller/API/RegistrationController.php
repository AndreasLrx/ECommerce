<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Entity\User;
use App\Controller\BaseController;
use App\Request\RegistrationRequest;

class RegistrationController extends BaseController
{
    #[Route('/register', methods: ['POST'])]
    public function register(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, RegistrationRequest $request, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        // Login must be unique
        if ($entityManager->getRepository(User::class)->findOneBy(["login" => $request->login]) != null)
            return $this->error("Login is already taken", Response::HTTP_UNPROCESSABLE_ENTITY);
        // Email must be unique
        else if ($entityManager->getRepository(User::class)->findOneBy(["email" => $request->email]) != null)
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

        $entityManager->persist($user);
        $entityManager->flush();


        return $this->json([
            'message' => 'Successfully registered as ' . $user->getLogin(),
            'token' => $JWTManager->create($user)
        ], Response::HTTP_CREATED);
    }
}
