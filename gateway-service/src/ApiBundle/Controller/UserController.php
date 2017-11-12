<?php

namespace ApiBundle\Controller;

use ApiBundle\Controller\Traits\ResponseTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class UserController extends FOSRestController
{
    use ResponseTrait;

    /**
     * @Rest\Get("/users/list")
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve list of users.",
     *     statusCodes={200="Returned when successful"}
     * )
     */
    public function getUsersListAction(): View
    {
        $uri = sprintf('%s/userslist?token=%s',
            $this->getParameter('users_host'), $this->getParameter('auth_token')
        );

        $response = $this->getResponse($uri);
        return new View($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * @Rest\Get("/sellers/list")
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve list of sellers.",
     *     statusCodes={200="Returned when successful"}
     * )
     */
    public function getSellersListAction(): View
    {
        $uri = sprintf('%s/sellerslist?token=%s',
            $this->getParameter('users_host'), $this->getParameter('auth_token')
        );

        $response = $this->getResponse($uri);
        return new View($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * @Rest\Get("/user/{id}")
     * @param int $id
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve specific user.",
     *     statusCodes={200="Returned when successful", 404="Returned when user not found"}
     * )
     */
    public function getUserAction(int $id): View
    {
        $uri = sprintf('%s/user/%d?token=%s',
            $this->getParameter('users_host'), $id, $this->getParameter('auth_token')
        );

        $response = $this->getResponse($uri);
        return new View($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * @Rest\Post("/users/register")
     * @param Request $request
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Register new user as buyer.",
     *     statusCodes={200="Returned when successful", 400="Returned when incorrect data passed"},
     *     parameters={
     *         {"name"="email", "dataType"="string", "description"="User email", "required"=true},
     *         {"name"="firstName", "dataType"="string", "description"="First name", "required"=true},
     *         {"name"="lastName", "dataType"="string", "description"="Last name", "required"=true},
     *         {"name"="plainPassword", "dataType"="string", "description"="Password for user", "required"=true},
     *         {"name"="addressLine1", "dataType"="string", "description"="Address 1", "required"=true},
     *         {"name"="addressLine2", "dataType"="string", "description"="Address 2", "required"=false},
     *         {"name"="zipCode", "dataType"="string", "description"="ZIP code", "required"=true},
     *         {"name"="city", "dataType"="string", "description"="City", "required"=true},
     *         {"name"="country", "dataType"="string", "description"="Country", "required"=true}
     *     }
     * )
     */
    public function registerUserAction(Request $request): View
    {
        $uri = sprintf('%s/new/user?token=%s&%s',
            $this->getParameter('users_host'), $this->getParameter('auth_token'), http_build_query($request->request->all())
        );

        $response = $this->postResponse($uri);
        return new View($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * @Rest\Post("/users/seller/register")
     * @param Request $request
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Register new user as seller.",
     *     statusCodes={200="Returned when successful", 400="Returned when incorrect data passed"},
     *     parameters={
     *         {"name"="email", "dataType"="string", "description"="User email", "required"=true},
     *         {"name"="firstName", "dataType"="string", "description"="First name", "required"=true},
     *         {"name"="lastName", "dataType"="string", "description"="Last name", "required"=true},
     *         {"name"="plainPassword", "dataType"="string", "description"="Password for user", "required"=true},
     *         {"name"="addressLine1", "dataType"="string", "description"="Address 1", "required"=true},
     *         {"name"="addressLine2", "dataType"="string", "description"="Address 2", "required"=false},
     *         {"name"="zipCode", "dataType"="string", "description"="ZIP code", "required"=true},
     *         {"name"="city", "dataType"="string", "description"="City", "required"=true},
     *         {"name"="country", "dataType"="string", "description"="Country", "required"=true}
     *     }
     * )
     */
    public function registerSellerAction(Request $request): View
    {
        $uri = sprintf('%s/new/seller?token=%s&%s',
            $this->getParameter('users_host'), $this->getParameter('auth_token'), http_build_query($request->request->all())
        );

        $response = $this->postResponse($uri);
        return new View($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * @Rest\Get("/users/delete/{id}")
     * @param int $id
     * @param Request $request
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Delete user.",
     *     statusCodes={200="Returned when successful", 404="Returned when item does not exist"}
     * )
     */
    public function deleteUserAction(int $id, Request $request): View
    {
        $uri = sprintf('%s/delete/%d?token=%s&%s',
            $this->getParameter('users_host'), $id, $this->getParameter('auth_token'), $request->getQueryString()
        );

        $response = $this->getResponse($uri);
        return new View($response->getBody()->getContents(), $response->getStatusCode());
    }
}