<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_TOKEN = 'test1';
    public function load(ObjectManager $manager)
    {
        $users = [
            [
                'name' => 'damian90',
                'email' => 'damian90@o2.pl',
                'password' => 'damian1',
                'apiToken' => 'test1'
            ],
            [
                'name' => 'damian91',
                'email' => 'damian91@o2.pl',
                'password' => 'damian2',
                'apiToken' => 'test2'
            ],
            [
                'name' => 'damian92',
                'email' => 'damian92@o2.pl',
                'password' => 'damian3',
                'apiToken' => 'test3'
            ]
        ];
        foreach ($users as $userRow) {
            $user = new User();
            $user->setName($userRow['name']);
            $user->setEmail($userRow['email']);
            $user->setPassword($userRow['password']);
            $user->setApiToken($userRow['apiToken']);
            $manager->persist($user);
        }

        $manager->flush();

    }
}