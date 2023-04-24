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
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

#[OA\Tag('Users', "Endpoints related to the current user informations")]
#[OA\Response(
    response: 401,
    ref: '#components/responses/UnauthorizedError'
)]
#[Route('/users')]
class UserController extends BaseController
{
    #[Route('', methods: ['GET'])]
    #[OA\Get(description: 'Fetch informations about the authentified user')]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new Model(type: User::class, groups: ["default"])
    )]
    public function get_current_user(UserRepository $repository): JsonResponse
    {
        return $this->json($this->get_user_entity($repository));
    }

    #[Route('', methods: ['PUT'])]
    #[OA\Put(description: 'Update informations about the authentified user')]
    #[OA\RequestBody(description: "New user informations", required: true, content: new Model(type: User::class, groups: ["default", "update"]))]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new Model(type: User::class, groups: ["default"])
    )]
    #[OA\Response(
        response: 422,
        description: "User informations are invalid",
        content: new OA\JsonContent(
            ref: '#components/schemas/GeneralError',
            examples:
            [
                new OA\Examples(
                    example: 'invalid_mail',
                    ref: '#components/examples/UserValidationErrorExample'
                )
            ]
        )
    )]
    public function update_current_user(UserRepository $repository, RegistrationRequest $request, UserPasswordHasherInterface $passwordHasher, ): JsonResponse
    {
        $user = $this->get_user_entity($repository);

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
            'user' => $user
        ]);
    }
}
