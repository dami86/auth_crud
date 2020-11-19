<?php


namespace App\Api;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

interface ICrud
{

    public function __construct(CrudHelper $crudHelper, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager);
    public function getOne(ServiceEntityRepository $repository, $id): JsonResponse;
    public function addUser(Request $request): JsonResponse;
    public function deleteUser(ServiceEntityRepository $repositoryFactory, $id): JsonResponse;
    public function updateUser(ServiceEntityRepository $repository, $id): JsonResponse;
    public function getAll(ServiceEntityRepository $repository): JsonResponse;

}