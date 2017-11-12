<?php

namespace ApiBundle\Controller;

use ApiBundle\Controller\Traits\ResponseTrait;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ItemsController extends FOSRestController
{
    use ResponseTrait;

    /**
     * @Rest\Get("/items/list")
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve list of all items.",
     *     statusCodes={200="Returned when successful"}
     * )
     */
    public function itemsListAction(): View
    {
        $uri = sprintf('%s/itemslist?token=%s',
            $this->getParameter('items_host'), $this->getParameter('auth_token')
        );

        $response = $this->getResponse($uri);

        return new View($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * @Rest\Get("/items/seller/{id}")
     * @param int $id
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve list seller items.",
     *     statusCodes={200="Returned when successful"}
     * )
     */
    public function getSellerItems(int $id): View
    {
        $uri = sprintf('%s/itemslist/seller/%d?token=%s',
            $this->getParameter('items_host'), $id, $this->getParameter('auth_token')
        );

        $response = $this->getResponse($uri);

        return new View($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * @Rest\Get("/items/view/{id}")
     * @param int $id
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve specific item information.",
     *     statusCodes={200="Returned when successful", 404="Returned when item does not exist"}
     * )
     */
    public function viewItemAction(int $id): View
    {
        $uri = sprintf('%s/view/%d?token=%s',
            $this->getParameter('items_host'), $id, $this->getParameter('auth_token')
        );

        $response = $this->getResponse($uri);

        return new View($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * @Rest\Post("/items/new")
     * @param Request $request
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Create new item.",
     *     statusCodes={200="Returned when successful",
     *         404="Returned when seller does not exist", 400="Returned when there are validation errors"
     *     },
     *      parameters={
     *         {"name"="sellerId", "dataType"="int", "description"="Seller id which creates item", "required"=true},
     *         {"name"="name", "dataType"="string", "description"="Item name", "required"=true},
     *         {"name"="description", "dataType"="string", "description"="Item description", "required"=true},
     *         {"name"="qty", "dataType"="int", "description"="Item quantity", "required"=true},
     *         {"name"="price", "dataType"="float", "description"="Item price", "required"=true}
     *     }
     * )
     */
    public function newItemAction(Request $request)
    {
        $userId = $request->get('sellerId');
        $userUri = sprintf('%s/user/%d?token=%s',
            $this->getParameter('users_host'), $userId, $this->getParameter('auth_token')
        );
        if ($this->getResponse($userUri)->getStatusCode() !== Response::HTTP_OK) {
            return new View('Seller not found', Response::HTTP_NOT_FOUND);
        }

        $canSellUri = sprintf('%s/check-seller/%d?token=%s',
            $this->getParameter('users_host'), $userId, $this->getParameter('auth_token')
        );

        if (!json_decode($this->getResponse($canSellUri)->getBody()->getContents())) {
            return new View('User is not seller', Response::HTTP_BAD_REQUEST);
        }

        $itemUri = sprintf('%s/items/new?token=%s&%s',
            $this->getParameter('items_host'), $this->getParameter('auth_token'), http_build_query($request->request->all())
        );

        $itemResponse = $this->postResponse($itemUri);

        return new View($itemResponse->getBody()->getContents(), $itemResponse->getStatusCode());
    }

    /**
     * @Rest\Post("/items/edit/{id}")
     * @param int $id
     * @param Request $request
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Edit existing item.",
     *     statusCodes={200="Returned when successful",
     *         404="Returned when seller or item does not exist",
     *         400="Returned when there are validation errors or given user id is not seller"
     *     },
     *     requirements={
     *         {"name"="id", "dataType"="int", "description"="Item id"},
     *     },
     *      parameters={
     *         {"name"="sellerId", "dataType"="int", "description"="Seller id which creates item", "required"=true},
     *         {"name"="name", "dataType"="string", "description"="Item name", "required"=false},
     *         {"name"="description", "dataType"="string", "description"="Item description", "required"=false},
     *         {"name"="qty", "dataType"="int", "description"="Item quantity", "required"=false},
     *         {"name"="price", "dataType"="float", "description"="Item price", "required"=false}
     *     }
     * )
     */
    public function editItemAction(int $id, Request $request)
    {
        $userId = $request->get('sellerId');
        $userUri = sprintf('%s/user/%d?token=%s',
            $this->getParameter('users_host'), $userId, $this->getParameter('auth_token')
        );

        if ($this->getResponse($userUri)->getStatusCode() !== Response::HTTP_OK) {
            return new View('Seller not found', Response::HTTP_NOT_FOUND);
        }

        $canSellUri = sprintf('%s/check-seller/%d?token=%s',
            $this->getParameter('users_host'), $userId, $this->getParameter('auth_token')
        );

        if (!json_decode($this->getResponse($canSellUri)->getBody()->getContents())) {
            return new View('User is not seller', Response::HTTP_BAD_REQUEST);
        }

        $uri = sprintf('%s/items/edit/%d?token=%s&%s',
            $this->getParameter('items_host'), $id, $this->getParameter('auth_token'), $request->getQueryString()
        );

        $itemResponse = $this->postResponse($uri);

        return new View($itemResponse->getBody()->getContents(), $itemResponse->getStatusCode());
    }

    /**
     * @Rest\Get("/items/remove/{id}")
     * @param int $id
     * @param Request $request
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Remove item.",
     *     statusCodes={200="Returned when successful", 404="Returned when item does not exist"}
     * )
     */
    public function removeItemAction(int $id, Request $request): View
    {
        $uri = sprintf('%s/items/delete/%d?token=%s&%s',
            $this->getParameter('items_host'), $id, $this->getParameter('auth_token'), $request->getQueryString()
        );
        $response = $this->getResponse($uri);

        return new View($response->getBody()->getContents(), $response->getStatusCode());
    }
}
