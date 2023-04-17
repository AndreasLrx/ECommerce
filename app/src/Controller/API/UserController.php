<?php

namespace App\Controller\API;

use App\Repository\UserRepository;
use App\Request\RegistrationRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Controller\BaseController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/users')]
class UserController extends BaseController
{
    #[Route('/', methods: ['GET'])]
    public function get_current_user(UserRepository $repository): JsonResponse
    {
        $user = $this->get_user_entity($repository);
        if ($user == null)
            return $this->error("Unable to fetch current user data", Response::HTTP_INTERNAL_SERVER_ERROR);

        return $this->json($user);
    }

    #[Route('/', methods: ['PUT'])]
    public function update_current_user(UserRepository $repository, RegistrationRequest $request, UserPasswordHasherInterface $passwordHasher, ): JsonResponse
    {
        $user = $this->get_user_entity($repository);
        if ($user == null)
            return $this->error("Unable to fetch current user data", Response::HTTP_INTERNAL_SERVER_ERROR);


        if ($user->getLogin() != $request->login) {
            if ($repository->findOneBy(['login' => $request->login]) != null)
                return $this->error("Login is already taken", Response::HTTP_UNPROCESSABLE_ENTITY);
            $user->setLogin($request->login);
        }
        if ($user->getPassword() != $request->password)
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $request->password
                )
            );
        if ($user->getEmail() != $request->email) {
            if ($repository->findOneBy(['email' => $request->email]) != null)
                return $this->error("Email is already taken", Response::HTTP_UNPROCESSABLE_ENTITY);
            $user->setEmail($request->email);
        }
        if ($user->getFirstname() != $request->firstname)
            $user->setFirstname($request->firstname);
        if ($user->getLastname() != $request->lastname)
            $user->setLastname($request->lastname);
        $repository->save($user, true);

        return $this->json([
            'message' => 'Successfully updated current user informations',
            'product' => $user
        ]);
    }
}
