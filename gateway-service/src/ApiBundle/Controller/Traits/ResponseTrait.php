<?php

namespace ApiBundle\Controller\Traits;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

trait ResponseTrait
{
    /**
     * @param string $uri
     * @return ResponseInterface|PromiseInterface
     */
    protected function getResponse(string $uri)
    {
        return $this->get('api.service_call')->sendRequest('GET', $uri);
    }

    /**
     * @param string $uri
     * @return ResponseInterface|PromiseInterface
     */
    protected function postResponse(string $uri)
    {
        return $this->get('api.service_call')->sendRequest('POST', $uri);
    }
}