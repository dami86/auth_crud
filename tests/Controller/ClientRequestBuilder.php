<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DomCrawler\Crawler;


class ClientRequestBuilder
{
    /**
     *
     * @var Client
     */
    private $client;

    /**
     *
     * @var string
     */
    private $method;

    /**
     *
     * @var string
     */
    private $uri;

    /**
     *
     * @var array
     */
    private $parameters;

    /**
     *
     * @var array
     */
    private $server;

    /**
     *
     * @var string
     */
    private $acceptType;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->parameters = [];
        $this->server = [];
        $this->acceptType = 'application/json';
    }

    public static function create(Client $client): ClientRequestBuilder
    {
        return new ClientRequestBuilder($client);
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    public function addParameter(string $key, $value): self
    {
        $this->parameters[$key] = $value;
        return $this;
    }

    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function addServerParameter(string $key, $value): self
    {
        $this->server[$key] = $value;
        return $this;
    }

    public function setServerParameters(array $parameters): self
    {
        $this->server = $parameters;
        return $this;
    }

    /**
     * @param string $acceptType
     */
    public function setAcceptType(string $acceptType): self
    {
        $this->acceptType = $acceptType;
        return $this;
    }

    /**
     * Executes the request and returns the crawler.
     */
    public function request(): Crawler
    {
        $this->server['HTTP_ACCEPT'] = $this->acceptType;
        return $this->client->request($this->method, $this->uri, $this->parameters, [], $this->server);
    }
}