<?php

namespace ApiBundle\Controller;

use ApiBundle\Controller\Traits\AuthTrait;
use ApiBundle\Controller\Traits\DoctrineTrait;
use ApiBundle\Entity\SaleOrder;
use ApiBundle\Entity\SaleOrderItem;
use ApiBundle\Form\SaleOrderItemType;
use ApiBundle\Form\SaleOrderType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends FOSRestController
{
    use DoctrineTrait, AuthTrait;

    /**
     * @Rest\Get("/list/buyer/{buyerId}")
     * @param int $buyerId
     * @param Request $request
     * @return View
     */
    public function getOrdersListAsBuyerAction(int $buyerId, Request $request): View
    {
        return $this->authorize($request->query->get('token')) ?? new View($this->repo(SaleOrder::class)->getBuyerOrders($buyerId));
    }

    /**
     * @Rest\Get("/list/seller/{sellerId}")
     * @param int $sellerId
     * @param Request $request
     * @return View
     */
    public function getOrdersListAsSellerAction(int $sellerId, Request $request): View
    {
        return $this->authorize($request->query->get('token')) ?? new View($this->repo(SaleOrder::class)->getSellerOrders($sellerId));
    }

    /**
     * @Rest\Get("/view/{id}")
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function getOrderAction(int $id, Request $request): View
    {
        if ($authorize = $this->authorize($request->query->get('token'))) {
            return $authorize;
        }

        $order = $this->repo(SaleOrder::class)->find($id);

        if (!$order) {
            return new View('Order does not exist', Response::HTTP_NOT_FOUND);
        }

        return new View($order, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/orders/order/items/view/{orderId}")
     * @param int $orderId
     * @param Request $request
     * @return View
     */
    public function getOrderItems(int $orderId, Request $request): View
    {
        if ($authorize = $this->authorize($request->query->get('token'))) {
            return $authorize;
        }

        $order = $this->repo(SaleOrder::class)->find($orderId);

        if (!$order) {
            return new View('Order does not exist', Response::HTTP_NOT_FOUND);
        }

        return new View($order->getOrderItems(), Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/new")
     * @param Request $request
     * @return View
     */
    public function newOrderAction(Request $request): View
    {
        if ($authorize = $this->authorize($request->query->get('token'))) {
            return $authorize;
        }

        $order = new SaleOrder();
        $form = $this->createForm(SaleOrderType::class, $order);
        $form->submit($request->query->all());

        if ($form->isValid()) {
            $order->setOrderStatus(SaleOrder::STATUS_NEW);
            if (!$request->get('dry-run')) {
                $this->persist($form->getData())->flush();
            }

            return new View($order->getId(), Response::HTTP_OK);
        }

        return new View($form->getErrors(true), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Post("/new/order-item/{orderId}")
     * @param int $orderId
     * @param Request $request
     * @return View
     */
    public function addOrderItemAction(int $orderId, Request $request): View
    {
        if ($authorize = $this->authorize($request->query->get('token'))) {
            return $authorize;
        }

        $order = $this->repo(SaleOrder::class)->find($orderId);

        if (!$order) {
            return new View('Order does not exist', Response::HTTP_NOT_FOUND);
        }

        $orderItem = new SaleOrderItem($order);

        $form = $this->createForm(SaleOrderItemType::class, $orderItem);
        $form->submit($request->query->all());

        if ($form->isValid()) {
            if (!$request->query->get('dry-run')) {
                $this->persist($form->getData())->flush();
            }

            return new View($orderItem->getId(), Response::HTTP_OK);
        }

        return new View($form->getErrors(true), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Get("/change-status/{orderId}")
     * @param int $orderId
     * @param Request $request
     * @return View
     */
    public function changeOrderStatus(int $orderId, Request $request): View
    {
        if ($authorize = $this->authorize($request->query->get('token'))) {
            return $authorize;
        }

        $order = $this->repo(SaleOrder::class)->find($orderId);

        if (!$order) {
            return new View('Order does not exist', Response::HTTP_NOT_FOUND);
        }

        $order->setOrderStatus(SaleOrder::$orderStatuses[$request->query->get('status')]);
        if ($request->query->get('dry-run')) {
            $this->flush();
        }

        return new View('Order status updated', Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/buyer-statuses")
     * @param Request $request
     * @return View
     */
    public function getBuyerStatuses(Request $request): View
    {
        if ($authorize = $this->authorize($request->query->get('token'))) {
            return $authorize;
        }

        return new View(SaleOrder::$buyerOrderStatuses, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/seller-statuses")
     * @param Request $request
     * @return View
     */
    public function getSellerStatuses(Request $request): View
    {
        if ($authorize = $this->authorize($request->query->get('token'))) {
            return $authorize;
        }

        return new View(SaleOrder::$sellerOrderStatuses, Response::HTTP_OK);
    }
}