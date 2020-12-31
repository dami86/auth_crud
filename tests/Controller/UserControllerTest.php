<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends RestTestBase
{
    public function testRetrieveUser()
    {
        $userName = "damian9442@o2.pl";
        $mail = 'damian332469@o2.pl';
        $password = 'damian';
        $data = [
            'name' => $userName,
            'email' => $mail,
            'roles' => ['ADMIN'],
            'password' => $password,
        ];

        $this->loadFixture(new UserFixtures());


        $this->createRequestBuilder()
            ->setMethod('POST')
            ->setUri('/api/users')
            ->setAcceptType('application/json')
            ->addServerParameter('HTTP_X-AUTH-TOKEN', UserFixtures::ADMIN_USER_TOKEN)
            ->setParameters($data)
            ->request();

        $response = $this->client->getResponse();
        self::assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            'the "Content-Type" header is "' . $response->headers->get('Content-Type') . '"' // optional message shown on failure
        );

        self::assertEquals(200, $response->getStatusCode(), 'Status code was ' . $response->getStatusCode() . ' but expected 200: ' . $response->getContent());
        self::assertEquals(200, json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR)['status']);
    }

}