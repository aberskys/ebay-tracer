<?php

namespace ApiBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

class ServiceCall
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $url URL path of the API method
     * @param string $callType Type of call (GET, POST, PUT, etc.)
     *
     * @return ResponseInterface|PromiseInterface
     */
    public function sendRequest(string $callType, string $url)
    {
        try {
            $response = $this->client->request($callType, $url);
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }
}