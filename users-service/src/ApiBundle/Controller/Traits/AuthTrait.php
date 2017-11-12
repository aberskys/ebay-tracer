<?php

namespace ApiBundle\Controller\Traits;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

trait AuthTrait
{
    /**
     * @param string $token
     * @return View|null
     */
    protected function authorize(string $token = null): ?View
    {
        if ($this->getParameter('auth_token') !== $token) {
            return new View('Authentication failed', Response::HTTP_UNAUTHORIZED);
        }

        return null;
    }
}
