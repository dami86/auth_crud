<?php

namespace App\Controller;

use App\Api\UserApi;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_user")
 */
class ApiUserController extends AbstractController
{

    /**
     * @param UserApi $userApi
     * @return JsonResponse
     * @Route ("/users", name="users", methods={"GET"})
     */
    public function getAll(UserApi $userApi): JsonResponse
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        return $userApi->getAll($repository);
    }

    /**
     * @Route("/users", name="users_add", methods={"POST"})
     * @param UserApi $user
     * @param Request $request
     * @return JsonResponse|null
     */
    public function addUser(UserApi $user, Request $request): ?JsonResponse
    {
        return $user->addUser($request);
    }

    /**
     * @Route("/users/{id}", name="users_delete", methods={"DELETE"})
     * @param $id
     * @param UserApi $userApi
     * @return JsonResponse
     */
    public function deleteUser($id, UserApi $userApi): JsonResponse
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        return $userApi->deleteUser($repository, $id);
    }

    /**
     *
     * @param $id
     * @param UserApi $userApi
     * @return JsonResponse
     * @Route("/users/{id}", name="users_put", methods={"PUT"})
     */
    public function updateUser( $id, UserApi $userApi): ?JsonResponse
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        return $userApi->updateUser($repository, $id);
    }

    /**
     *
     * @param $id
     * @param UserApi $userApi
     * @return JsonResponse
     * @Route("/users/{id}", name="users_get", methods={"GET"})
     */
    public function getOne($id, UserApi $userApi): JsonResponse
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        return $userApi->getOne($repository,$id);
    }

    public function response(array $data, $status = 200, $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }
}
