<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api", name="api_user")
 */
class ApiUserController extends AbstractController
{

    /**
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @Route ("/users", name="users", methods={"GET"})
     */
    public function getUsers(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        return $this->response($users);
    }

    /**
     * @Route("/users", name="users_add", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return JsonResponse|null
     */
    public function addUser(Request $request, UserPasswordEncoderInterface $userPasswordEncoder): ?JsonResponse
    {
        try {
            $request = $this->transformJsonBody($request);
            if (!$request
                || !$request->get('email')
                || !$request->get('name')
                || !$request->get('roles')
                || !$request->get('password')
            ) {
                throw new \Exception();
            }

            $user = new User();

            $user->setEmail($request->get('email'));
            $user->setName($request->get('name'));
            $user->setRoles($request->get('roles'));
            $user->setPassword($userPasswordEncoder->encodePassword($user, $request->get('password')));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $data = [
                'status' => 200,
                'message' => "User added successfully"
            ];
            return $this->response($data);

        } catch (\Exception $exception) {
            $data = [
                'status' => 422,
                'message' => "Invalidate data"
            ];
            return $this->response($data, 422);
        }
    }

    /**
     * @Route("/users/{id}", name="users_delete", methods={"DELETE"})
     * @param UserRepository $userRepository
     * @param $id
     * @return JsonResponse
     */
    public function deleteUser(UserRepository $userRepository, $id): JsonResponse
    {
            $user = $userRepository->find($id);

            if (!$user) {
                return $this->userNotFound();
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            $data = [
                'status' => 200,
                'message' => 'User deleted successfully'
            ];
            return $this->response($data);
    }

    /**
     *
     * @param $id
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return JsonResponse
     * @Route("/users/{id}", name="users_put", methods={"PUT"})
     */
    public function updateUser(Request $request, $id,
        UserPasswordEncoderInterface $userPasswordEncoder): ?JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        try {
            $user = $em->getRepository(User::class)->find($id);
            if (!$user) {
                return $this->userNotFound();
            }

            $request = $this->transformJsonBody($request);

            if (!$request
                || !$request->get('email')
                || !$request->get('name')
                || !$request->get('roles')
                || !$request->get('password')
            ) {
                throw new \Exception();
            }


            $user->setEmail($request->get('email'));
            $user->setName($request->get('name'));
            $user->setRoles($request->get('roles'));
            $user->setPassword($userPasswordEncoder->encodePassword($user, $request->get('password')));

            $em->flush();

            $data = [
                'status' => 200,
                'message' => "User updated successfully"
            ];
            return $this->response($data);

        } catch (\Exception $exception) {
            $data = [
                'status' => 422,
                'message' => "Invalidate data"
            ];
            return $this->response($data, 422);
        }

    }

    /**
     *
     * @param UserRepository $userRepository
     * @param $id
     * @return JsonResponse
     * @Route("/users/{id}", name="users_get", methods={"GET"})
     */
    public function getUserApi(UserRepository $userRepository, $id): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            $this->userNotFound();
        }

        $data = [
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
            'id' => $user->getId()
        ];
        return $this->response($data);
    }

    public function response(array $data, $status = 200, $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    protected function transformJsonBody(Request $request): Request
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

    /**
     * @return JsonResponse
     */
    protected function userNotFound(): JsonResponse
    {
        $data = [
            'status' => 404,
            'message' => "User not found"
        ];
        return $this->response($data, 404);
    }
}
