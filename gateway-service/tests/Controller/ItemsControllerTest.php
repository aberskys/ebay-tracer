<?php

namespace ApiBundle\Controller;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ItemsControllerTest extends TestCase
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
            'name' => 'Test item',
            'description' => 'Test item description',
            'qty' => 10,
            'price' => 10.00,
            'sellerId' => 1,
            'dry-run' => true
        ];
    }

    public function testItemsListShouldReturnOk()
    {
        $response = $this->client->get('gateway/items/list');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetSellerItemsShouldReturnOk()
    {
        $response = $this->client->get('gateway/items/seller/1');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testViewItemShouldReturnNotFound()
    {
        $response = $this->client->get('gateway/items/view/0');
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains('Item does not exist', $response->getBody()->getContents());
    }

    public function testViewItemShouldReturnOk()
    {
        $response = $this->client->get('gateway/items/view/1');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testNewItemShouldReturnSellerNotFound()
    {
        $this->requestParams['sellerId'] = 0;
        $response = $this->client->post('gateway/items/new', ['form_params' => $this->requestParams]);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains('Seller not found', $response->getBody()->getContents());
    }

    public function testNewItemShouldReturnUserNotSeller()
    {
        $this->requestParams['sellerId'] = 2;
        $response = $this->client->post('gateway/items/new', ['form_params' => $this->requestParams]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('User is not seller', $response->getBody()->getContents());
    }

    public function testNewItemShouldReturnOk()
    {
        $this->requestParams['sellerId'] = 1;
        $response = $this->client->post('gateway/items/new', ['form_params' => $this->requestParams]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Item successfully created', $response->getBody()->getContents());
    }

    public function testEditItemShouldReturnSellerNotFound()
    {
        $this->requestParams['sellerId'] = 0;
        $response = $this->client->post('gateway/items/edit/1', ['form_params' => $this->requestParams]);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains('Seller not found', $response->getBody()->getContents());
    }

    public function testEditItemShouldReturnUserNotSeller()
    {
        $this->requestParams['sellerId'] = 2;
        $response = $this->client->post('gateway/items/edit/1', ['form_params' => $this->requestParams]);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('User is not seller', $response->getBody()->getContents());
    }

    public function testEditItemShouldReturnOk()
    {
        $this->requestParams['sellerId'] = 1;
        $response = $this->client->post('gateway/items/edit/1', ['form_params' => $this->requestParams]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Item successfully updated', $response->getBody()->getContents());
    }

    public function testRemoveItemShouldReturnItemNotFound()
    {
        $response = $this->client->get('gateway/items/remove/0?dry-run=1');
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains('Item does not exist', $response->getBody()->getContents());
    }

    public function testRemoveItemShouldReturnOk()
    {
        $response = $this->client->get('gateway/items/remove/3?dry-run=1');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
