<?php

namespace ApiBundle\Controller;

use ApiBundle\Controller\Traits\ResponseTrait;
use ApiBundle\Order\Item;
use ApiBundle\Order\Order;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends FOSRestController
{
    use ResponseTrait;

    /**
     * @Rest\Get("/orders/list/buyer/{id}")
     * @param int $id
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve list of buyer orders.",
     *     statusCodes={200="Returned when successful"},
     *     requirements={
     *         {"name"="id", "dataType"="int", "description"="Buyer id"}
     *     }
     * )
     */
    public function getOrdersListForBuyerAction(int $id): View
    {
        $uri = sprintf('%s/list/buyer/%d?token=%s',
            $this->getParameter('orders_host'), $id, $this->getParameter('auth_token')
        );
        $response = $this->getResponse($uri);

        return new View($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * @Rest\Get("/orders/list/seller/{id}")
     * @param int $id
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve list of seller orders.",
     *     statusCodes={200="Returned when successful"},
     *     requirements={
     *         {"name"="id", "dataType"="int", "description"="Seller id"}
     *     }
     * )
     */
    public function getOrdersListForSellerAction(int $id): View
    {
        $uri = sprintf('%s/list/seller/%d?token=%s',
            $this->getParameter('orders_host'), $id, $this->getParameter('auth_token')
        );

        $response = $this->getResponse($uri);

        return new View($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * @Rest\Post("/order/new/{id}")
     * @param int $id
     * @param Request $request
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Create new order.",
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when item, seller or buyer not found",
     *         400="Returned when requested quantity too high or there are validation errors"
     *     },
     *     requirements={
     *         {"name"="id", "dataType"="int", "description"="Item id for buying"},
     *     },
     *     parameters={
     *         {"name"="qty", "dataType"="int", "description"="Quantity", "required"=true},
     *         {"name"="buyerId", "dataType"="int", "description"="Buyer id", "required"=true},
     *         {"name"="sellerId", "dataType"="int", "description"="Seller id", "required"=true},
     *     }
     * )
     */
    public function newOrderAction(int $id, Request $request): View
    {
        $token = $this->getParameter('auth_token');
        $itemUri = sprintf('%s/view/%d?token=%s', $this->getParameter('items_host'), $id, $token);
        $itemResponse = $this->getResponse($itemUri);

        if ($itemResponse->getStatusCode() === Response::HTTP_NOT_FOUND) {
            return new View('Item requested for order was not found. Item id: ' . $id, Response::HTTP_NOT_FOUND);
        }
        $item = new Item(get_object_vars(json_decode($itemResponse->getBody()->getContents())));
        $qtyToBuy = $request->get('qty');

        // Create new order
        $orderUri = sprintf('%s/new?token=%s&%s',
            $this->getParameter('orders_host'), $token, http_build_query($request->request->all())
        );

        $orderResponse = $this->postResponse($orderUri);
        $order = $orderResponse->getBody()->getContents();

        if ($orderResponse->getStatusCode() !== Response::HTTP_OK) {
            return new View($order, $orderResponse->getStatusCode());
        }

        if ($qtyToBuy > $item->getQty()) {
            return new View('Required quantity is too high. Item id: ' . $id, Response::HTTP_BAD_REQUEST);
        }

        $orderItemQuery = http_build_query([
            'name'=> $item->getName(),
            'description' => $item->getDescription(),
            'qty' => $qtyToBuy,
            'price' => $item->getPrice(),
        ]);

        $orderItemUri = sprintf('%s/new/order-item/%d?token=%s&%s',
            $this->getParameter('orders_host'), $order, $token, $orderItemQuery
        );

        $orderItemResponse = $this->postResponse($orderItemUri);
        if ($orderItemResponse->getStatusCode() !== Response::HTTP_OK) {
            return new View($orderItemResponse->getBody()->getContents(), $orderItemResponse->getStatusCode());
        }
        // Deduct quantity from item
        $deductionUri = sprintf('%s/items/edit/%d?token=%s&qty=%s',
            $this->getParameter('items_host'), $id, $token, $item->getRemainingQty($qtyToBuy)
        );

        // Save remaining item quantity
        $deductionResponse = $this->postResponse($deductionUri);
        if ($deductionResponse->getStatusCode() !== Response::HTTP_OK) {
            return new View($deductionResponse->getBody()->getContents(), $deductionResponse->getStatusCode());
        }

        return new View('Order successfully created. Id:' . $order, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/order/view/{id}")
     * @param int $id
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Get specific order information.",
     *     statusCodes={200="Returned when successful", 404="Returned when order does not exist"}
     * )
     */
    public function getOrderAction(int $id): View
    {
        $uri = sprintf('%s/view/%d?token=%s',
            $this->getParameter('orders_host'), $id, $this->getParameter('auth_token')
        );

        $response = $this->getResponse($uri);

        return new View($response->getBody()->getContents(), $response->getStatusCode());
    }

    /**
     * @Rest\Get("/order/status-change/{id}/{userId}")
     * @param int $id
     * @param int $userId
     * @param Request $request
     * @return View
     * @ApiDoc(
     *     resource=true,
     *     description="Change order status.",
     *     statusCodes={200="Returned when successful", 400="Returned when user, order or status does not exist"},
     *     parameters={
     *         {"name"="status", "dataType"="string",
     *             "description"="Allowed values: STATUS_NEW, STATUS_PAID, STATUS_SHIPPED, STATUS_RECEIVED, STATUS_CANCELED",
     *             "required"=true
     *         },
     *     }
     * )
     */
    public function changeStatusAction(int $id, int $userId, Request $request): View
    {
        $status = $request->query->get('status');

        $buyerStatusesUri = sprintf('%s/buyer-statuses?token=%s',
            $this->getParameter('orders_host'), $this->getParameter('auth_token')
        );
        $buyerStatuses = (array) json_decode($this->getResponse($buyerStatusesUri)->getBody()->getContents());

        $sellerStatusesUri = sprintf('%s/seller-statuses?token=%s',
            $this->getParameter('orders_host'), $this->getParameter('auth_token')
        );
        $sellerStatuses = (array) json_decode($this->getResponse($sellerStatusesUri)->getBody());
        if (!isset($buyerStatuses[$status]) && !isset($sellerStatuses[$status])) {
            return new View('Undefined status', Response::HTTP_BAD_REQUEST);
        }

        $orderUri = sprintf('%s/view/%d?token=%s',
            $this->getParameter('orders_host'), $id, $this->getParameter('auth_token')
        );

        $orderResponse = $this->getResponse($orderUri);

        if ($orderResponse->getStatusCode() !== Response::HTTP_OK) {
            return new View($orderResponse->getBody()->getContents());
        }
        $order = new Order(get_object_vars(json_decode($orderResponse->getBody()->getContents())));

        if ($userId === $order->getBuyerId() || $userId === $order->getSellerId()) {
            if ($userId === $order->getBuyerId() && !isset($buyerStatuses[$status])) {
                return new View('Buyer cannot set this status', Response::HTTP_BAD_REQUEST);
            }

            if ($userId === $order->getSellerId() && !isset($sellerStatuses[$status])) {
                return new View('Seller cannot set this status', Response::HTTP_BAD_REQUEST);
            }

            $changeStatusUri = sprintf('%s/change-status/%d?token=%s&%s',
                $this->getParameter('orders_host'), $id, $this->getParameter('auth_token'), $request->getQueryString()
            );

            $changeStatusResponse = $this->getResponse($changeStatusUri);
            return new View($changeStatusResponse->getBody()->getContents(), $changeStatusResponse->getStatusCode());
        }

        return new View('Given user does not belong to this order', Response::HTTP_BAD_REQUEST);
    }
}
