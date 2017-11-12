<?php

namespace tests\Controller;

use ApiBundle\Repository\UserRepository;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ItemControllerTest extends TestCase
{
    const TOKEN = 'secretTokenForCommunications';
    const AUTHENTICATION_FAILED = 'Authentication failed';
    const ITEM_DOES_NOT_EXIST = 'Item does not exist';

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
        ];
    }

    public function testGetItemsListShouldThrowUnauthorizedException()
    {
        $response = $this->client->get('items/itemslist');

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testGetItemsListShouldReturnOk()
    {
        $response = $this->client->get('items/itemslist?token=' . self::TOKEN);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetSellerItemsListShouldThrowUnauthorizedException()
    {
        $response = $this->client->get('items/itemslist/seller/1');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testGetSellerItemsListShouldReturnOk()
    {
        $response = $this->client->get('items/itemslist/seller/1?token=' . self::TOKEN);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetItemActionShouldThrowUnauthorizedException()
    {
        $response = $this->client->get('items/view/1');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testGetItemActionShouldThrowNotFound()
    {
        $response = $this->client->get('items/view/0?token=' . self::TOKEN);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(self::ITEM_DOES_NOT_EXIST, json_decode($response->getBody()->getContents()));
    }

    public function testGetItemActionShouldReturnOk()
    {
        $response = $this->client->get('items/view/1?token=' . self::TOKEN);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testNewItemShouldThrowUnauthorizedException()
    {
        $response = $this->client->post('items/items/new');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testNewItemAShouldThrowValidationError()
    {
        unset($this->requestParams['name']);
        $response = $this->client->post('items/items/new?dry-run=1&token=' . self::TOKEN . '&' . http_build_query($this->requestParams));
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Name cannot be blank', $response->getBody()->getContents());

    }

    public function testNewItemShouldReturnOk()
    {
        $response = $this->client->post('items/items/new?dry-run=1&token=' . self::TOKEN . '&' . http_build_query($this->requestParams));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Item successfully created', json_decode($response->getBody()->getContents()));
    }

    public function testEditItemShouldThrowUnauthorizedException()
    {
        $response = $this->client->post('items/items/edit/1');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testEditItemAShouldThrowItemNotFoundError()
    {
        $response = $this->client->post('items/items/edit/0?token=' . self::TOKEN);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(self::ITEM_DOES_NOT_EXIST, json_decode($response->getBody()->getContents()));
    }

    public function testEditItemAShouldThrowValidationError()
    {
        $this->requestParams['qty'] = -10;
        $response = $this->client->post('items/items/edit/1?dry-run=1&token=' . self::TOKEN . '&' . http_build_query($this->requestParams));
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Value must be 0 or greater', $response->getBody()->getContents());
    }

    public function testEditItemShouldReturnOk()
    {
        $this->requestParams['qty'] = 10;
        $response = $this->client->post('items/items/edit/1?dry-run=1&token=' . self::TOKEN . '&' . http_build_query($this->requestParams));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Item successfully updated', $response->getBody()->getContents());
    }

    public function testRemoveItemShouldThrowUnauthorizedException()
    {
        $response = $this->client->get('items/items/delete/1');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testRemoveItemAShouldThrowItemNotFoundError()
    {
        $response = $this->client->get('items/items/delete/0?token=' . self::TOKEN);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(self::ITEM_DOES_NOT_EXIST, json_decode($response->getBody()->getContents()));
    }

    public function testRemoveItemShouldReturnOk()
    {
        $response = $this->client->get('items/items/delete/1?dry-run=1&token=' . self::TOKEN);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Item successfully removed', $response->getBody()->getContents());
    }
}
