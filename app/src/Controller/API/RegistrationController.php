<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Entity\User;
use App\Controller\BaseController;

class RegistrationController extends BaseController
{
    #[Route('/register', methods: ['POST'])]
    public function register(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Request $request, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        if ($entityManager->getRepository(User::class)->findOneBy(["login" => $parameters['login']]) != null)
            return $this->error("Login already taken", Response::HTTP_BAD_REQUEST);

        $user = new User();
        $user->setLogin($parameters['login']);
        $user->setEmail($parameters['email']);
        $user->setFirstname($parameters['firstname']);
        $user->setLastname($parameters['lastname']);
        // hash the password (based on the security.yaml config for the $user class)
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $parameters['password']
            )
        );

        $entityManager->persist($user);
        $entityManager->flush();


        return $this->json([
            'message' => 'Successfully registered as ' . $user->getLogin(),
            'token' => $JWTManager->create($user)
        ]);
    }
}
