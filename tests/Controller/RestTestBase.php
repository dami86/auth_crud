<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Security\ApiTokenAuthenticator;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RestTestBase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /** @var EntityManager $manager */
    private $manager;
    /** @var ORMExecutor $executor */
    private $executor;

    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->manager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->executor = new ORMExecutor($this->manager, new ORMPurger());

        // Run the schema update tool using our entity metadata
        $schemaTool = new SchemaTool($this->manager);
        $schemaTool->updateSchema($this->manager->getMetadataFactory()->getAllMetadata());
    }

    protected function runCommand($command)
    {
        $application = new Application($this->client->getKernel());
        $application->setAutoExit(false);
        return $application->run(new StringInput($command), new NullOutput());
    }

    protected function createRequestBuilder(string $acceptType = 'application/json'): ClientRequestBuilder
    {
        $builder = new ClientRequestBuilder($this->client);
        $builder->setAcceptType($acceptType);
        return $builder;
    }

    protected function retrieveEntityManager(): EntityManagerInterface
    {
        return $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function loadFixture($fixture)
    {
        $loader = new Loader();
        $fixtures = is_array($fixture) ? $fixture : [$fixture];
        foreach ($fixtures as $item) {
            $loader->addFixture($item);
        }
        $this->executor->execute($loader->getFixtures());
    }

    public function tearDown()
    {
        (new SchemaTool($this->manager))->dropDatabase();
    }
}