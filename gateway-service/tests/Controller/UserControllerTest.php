<?php

namespace ApiBundle\Controller;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{
    const USER_DOES_NOT_EXIST = 'User does not exist';

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
            'email' => 'demo_email@app.dev',
            'plainPassword' => 'password',
            'firstName' => 'Test',
            'lastName' => 'User',
            'addressLine1' => 'Address',
            'addressLine2' => 'Line2',
            'city' => 'City',
            'country' => 'country',
            'zipCode' => 'zipCode',
            'dry-run' => true,
        ];
    }

    public function testGetUsersListShouldReturnOk()
    {
        $response = $this->client->get('gateway/users/list');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetSellersListShouldReturnOk()
    {
        $response = $this->client->get('gateway/sellers/list');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetUserShouldReturnNotFound()
    {
        $response = $this->client->get('gateway/user/0');
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains(self::USER_DOES_NOT_EXIST, json_decode($response->getBody()->getContents()));
    }

    public function testGetUserShouldReturnOk()
    {
        $response = $this->client->get('gateway/user/1');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRegisterUserShouldReturnOk()
    {
        $this->requestParams['dry-run'] = true;
        $response = $this->client->post('gateway/users/register', ['form_params' => $this->requestParams]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('User successfully created', $response->getBody()->getContents());
    }

    public function testRegisterUserShouldThrowValidationError()
    {
        unset($this->requestParams['plainPassword']);
        $response = $this->client->post('gateway/users/register', ['form_params' => $this->requestParams]);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Password cannot be empty', $response->getBody()->getContents());
    }

    public function testRegisterSellerShouldReturnOk()
    {
        $response = $this->client->post('gateway/users/seller/register', ['form_params' => $this->requestParams]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Seller successfully created', $response->getBody()->getContents());
    }

    public function testRegisterSellerShouldThrowValidationError()
    {
        unset($this->requestParams['plainPassword']);
        $response = $this->client->post('gateway/users/seller/register', ['form_params' => $this->requestParams]);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Password cannot be empty', $response->getBody()->getContents());
    }

    public function testDeleteUserShouldReturnOk()
    {
        $response = $this->client->get('gateway/users/delete/1?dry-run=1');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('User was deleted', $response->getBody()->getContents());
    }

    public function testDeleteUserShouldThrowUserNotFoundException()
    {
        $response = $this->client->get('gateway/users/delete/0?dry-run=1');
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains(self::USER_DOES_NOT_EXIST, $response->getBody()->getContents());
    }
}
