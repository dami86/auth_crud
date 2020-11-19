<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserApi implements ICrud
{

    private $crudHelper;
    private $entityManager;
    private $userPasswordEncoder;


    public function __construct(CrudHelper $crudHelper, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager)
    {
        $this->crudHelper = $crudHelper;
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function getOne(ServiceEntityRepository $repository, $id): JsonResponse
    {
        $user = $repository->find($id);

        if (!$user) {
            $this->crudHelper->userNotFound();
        }

        $data = [
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
            'id' => $user->getId()
        ];
        return $this->crudHelper->response($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addUser(Request $request): JsonResponse
    {
        try {
            $request = $this->crudHelper->transformJsonBody();
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
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $request->get('password')));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $data = [
                'status' => 200,
                'message' => "User added successfully"
            ];
            return $this->crudHelper->response($data);

        } catch (\Exception $exception) {
            $data = [
                'status' => 422,
                'message' => "Invalidate data"
            ];
            return $this->crudHelper->response($data, 422);
        }
    }

    public function deleteUser(ServiceEntityRepository $repository, $id): JsonResponse
    {
        $user = $repository->find($id);

        if (!$user) {
            return $this->crudHelper->userNotFound();
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $data = [
            'status' => 200,
            'message' => 'User deleted successfully'
        ];
        return $this->crudHelper->response($data);
    }

    public function updateUser(ServiceEntityRepository $repository, $id): JsonResponse
    {

        try {
            $user = $repository->find($id);
            if (!$user) {
                return $this->crudHelper->userNotFound();
            }

            $request = $this->crudHelper->transformJsonBody();

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
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $request->get('password')));

            $this->entityManager->flush();

            $data = [
                'status' => 200,
                'message' => "User updated successfully"
            ];
            return $this->crudHelper->response($data);

        } catch (\Exception $exception) {
            $data = [
                'status' => 422,
                'message' => "Invalidate data"
            ];
            return $this->crudHelper->response($data, 422);
        }
    }

    public function getAll(ServiceEntityRepository $repository): JsonResponse
    {
        $users = $repository->findAll();
        return $this->crudHelper->response($users);
    }
}