<?php

namespace ApiBundle\Controller;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class OrderControllerTest extends TestCase
{
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
            'qty' => 1,
        ];
    }
    
    public function testGetOrdersListForBuyerShouldReturnOk()
    {
        $response = $this->client->get('gateway/orders/list/buyer/0');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetOrdersListForSellerShouldReturnOk()
    {
        $response = $this->client->get('gateway/orders/list/buyer/0');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testNewOrderShouldReturnItemNotFound()
    {
        $response = $this->client->post('gateway/order/new/0', ['form_params' => $this->requestParams]);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains('Item requested for order was not found', $response->getBody()->getContents());
    }

    public function testNewOrderShouldReturnQuantityTooHigh()
    {
        $this->requestParams['qty'] = 99999999;
        $response = $this->client->post('gateway/order/new/1', ['form_params' => $this->requestParams]);
        $this->assertContains('Required quantity is too high', $response->getBody()->getContents());
    }

    public function testNewOrderShouldReturnOk()
    {
        $response = $this->client->post('gateway/order/new/1', ['form_params' => $this->requestParams]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Order successfully created', $response->getBody()->getContents());
    }

    public function testGetOrderShouldReturnOrderNotFound()
    {
        $response = $this->client->get('gateway/order/view/0');
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains('Order does not exist', $response->getBody()->getContents());
    }

    public function testGetOrderShouldReturnOk()
    {
        $response = $this->client->get('gateway/order/view/1');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testChangeStatusShouldReturnUndefinedStatusError()
    {
        $response = $this->client->get('gateway/order/status-change/1/2?status=STATUS_UNDEFINED');
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Undefined status', $response->getBody()->getContents());
    }

    public function testChangeStatusShouldReturnNotBuyerError()
    {
        $response = $this->client->get('gateway/order/status-change/1/2?status=STATUS_SHIPPED');
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Buyer cannot set this status', $response->getBody()->getContents());
    }

    public function testChangeStatusShouldReturnNotSellerError()
    {
        $response = $this->client->get('gateway/order/status-change/1/1?status=STATUS_PAID');
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Seller cannot set this status', $response->getBody()->getContents());
    }

    public function testChangeStatusShouldReturnOrderDoesNotBelongToUserError()
    {
        $response = $this->client->get('gateway/order/status-change/1/3?status=STATUS_PAID');
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Given user does not belong to this order', $response->getBody()->getContents());
    }

    public function testChangeStatusShouldReturnOk()
    {
        $response = $this->client->get('gateway/order/status-change/1/2?status=STATUS_PAID');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
