<?php

namespace tests\Controller;

use ApiBundle\Entity\SaleOrder;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class OrderControllerTest extends TestCase
{
    const TOKEN = 'secretTokenForCommunications';
    const AUTHENTICATION_FAILED = 'Authentication failed';
    const ORDER_DOES_NOT_EXIST = 'Order does not exist';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $requestParams;

    public function setUp()
    {
        $this->client = new Client([
            'http_errors' => false
        ]);

        $this->requestParams = [
            'buyerId' => 2,
            'sellerId' => 1,
            'orderStatus' => SaleOrder::STATUS_NEW,
        ];
    }

    public function testGetOrdersListAsBuyerShouldThrowUnauthorizedException()
    {
        $response = $this->client->get('orders/list/buyer/1');

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));

    }

    public function testGetOrdersListAsBuyerShouldReturnOk()
    {
        $response = $this->client->get('orders/list/buyer/1?token=' . self::TOKEN);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetOrdersListAsSellerShouldThrowUnauthorizedException()
    {
        $response = $this->client->get('orders/list/seller/1');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testGetOrdersListAsSellerShouldReturnOk()
    {
        $response = $this->client->get('orders/list/seller/1?token=' . self::TOKEN);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetOrderShouldThrowUnauthorizedException()
    {
        $response = $this->client->get('orders/view/1');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testGetOrderShouldThrowOrderNotFoundException()
    {
        $response = $this->client->get('orders/view/0?token=' . self::TOKEN);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(self::ORDER_DOES_NOT_EXIST, json_decode($response->getBody()->getContents()));
    }

    public function testGetOrderShouldReturnOk()
    {
        $response = $this->client->get('orders/view/1?token=' . self::TOKEN);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetOrderItemsShouldThrowUnauthorizedException()
    {
        $response = $this->client->get('orders/orders/order/items/view/1');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testGetOrderItemsShouldThrowOrderNotFoundException()
    {
        $response = $this->client->get('orders/orders/order/items/view/0?token=' . self::TOKEN);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(self::ORDER_DOES_NOT_EXIST, json_decode($response->getBody()->getContents()));
    }

    public function testGetOrderItemsShouldReturnOk()
    {
        $response = $this->client->get('orders/orders/order/items/view/1?token=' . self::TOKEN);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testNewOrderShouldThrowUnauthorizedException()
    {
        $response = $this->client->post('orders/new');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testNewOrderShouldThrowValidationErrors()
    {
        unset($this->requestParams['buyerId']);
        $response = $this->client->post('orders/new?token=' .self::TOKEN . '&' . http_build_query($this->requestParams));
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Buyer cannot be null', $response->getBody()->getContents());
    }

    public function testNewOrderShouldReturnOk()
    {
        $response = $this->client->post('orders/new?dry-run=1&token=' . self::TOKEN . '&' . http_build_query($this->requestParams));
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testAddOrderItemShouldThrowUnauthorizedException()
    {
        $response = $this->client->post('orders/new/order-item/1');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testAddOrderItemShouldThrowOrderNotFound()
    {
        $response = $this->client->post('orders/new/order-item/0?token=' . self::TOKEN);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(self::ORDER_DOES_NOT_EXIST, json_decode($response->getBody()->getContents()));
    }

    public function testAddOrderItemShouldReturnOk()
    {
        $orderItemRequest = [
            'name' => 'Demo item',
            'description' => 'desc',
            'qty' => 1,
            'price' => 10.99
        ];
        $response = $this->client->post('orders/new/order-item/1?dry-run=1&token=' .self::TOKEN . '&' . http_build_query($orderItemRequest));
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testChangeOrderStatusShouldThrowUnauthorizedException()
    {
        $response = $this->client->get('orders/change-status/1');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testChangeOrderStatusShouldThrowOrderNotFound()
    {
        $response = $this->client->get('orders/change-status/0?token=' . self::TOKEN);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(self::ORDER_DOES_NOT_EXIST, json_decode($response->getBody()->getContents()));
    }

    public function testChangeOrderStatusShouldReturnOk()
    {
        $response = $this->client->get('orders/change-status/1?dry-run=1&token=' . self::TOKEN . '&status=STATUS_PAID');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Order status updated', $response->getBody()->getContents());
    }

    public function testGetBuyerStatusesShouldThrowUnauthorizedException()
    {
        $response = $this->client->get('orders/buyer-statuses');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testGetBuyerStatusesShouldReturnOk()
    {
        $response = $this->client->get('orders/buyer-statuses?token=' . self::TOKEN);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetSellerStatusesShouldThrowUnauthorizedException()
    {
        $response = $this->client->get('orders/seller-statuses');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testGetSellerStatusesShouldReturnOk()
    {
        $response = $this->client->get('orders/seller-statuses?token=' . self::TOKEN);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
