<?php

namespace tests\Controller;

use ApiBundle\Repository\UserRepository;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class UserControllerTest extends TestCase
{
    const TOKEN = 'secretTokenForCommunications';
    const AUTHENTICATION_FAILED = 'Authentication failed';
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
        ];
    }

    public function testShouldThrowUnauthorizedWhenNoTokenGivenInUserListAction()
    {
        $response = $this->client->get('users/userslist');

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testShouldReturnOkOnUserslist()
    {
        $response = $this->client->get('users/userslist?token=' . self::TOKEN);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetUserShouldThrowUnauthorizedWhenNoTokenGiven()
    {
        $response = $this->client->get('users/user/1');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testGetUserShouldThrowUserNotFoundWhenIncorrectIdGiven()
    {
        $response = $this->client->get('users/user/0?token=' . self::TOKEN);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(self::USER_DOES_NOT_EXIST, json_decode($response->getBody()->getContents()));
    }

    public function testGetUserShouldReturnOk()
    {
        $response = $this->client->get('users/user/1?token=' . self::TOKEN);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testNewUserShouldThrowUnauthorizedWhenNoTokenGiven()
    {
        $response = $this->client->post('users/new/user');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testNewUserShouldThrowValidationError()
    {
        unset($this->requestParams['plainPassword']);
        $response = $this->client->post('users/new/user?dry-run=1&token=' . self::TOKEN . '&' . http_build_query($this->requestParams));
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Password cannot be empty', $response->getBody()->getContents());
    }

    public function testNewUserShouldReturnOk()
    {
        $response = $this->client->post('users/new/user?dry-run=1&token=' . self::TOKEN . '&' . http_build_query($this->requestParams));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('User successfully created', json_decode($response->getBody()->getContents()));
    }

    public function testNewSellerShouldThrowUnauthorizedWhenNoTokenGiven()
    {
        $response = $this->client->post('users/new/seller');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testNewSellerShouldThrowValidationError()
    {
        unset($this->requestParams['plainPassword']);
        $response = $this->client->post('users/new/seller?dry-run=1&token=' . self::TOKEN . '&' . http_build_query($this->requestParams));
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Password cannot be empty', $response->getBody()->getContents());
    }

    public function testNewSellerShouldReturnOk()
    {
        $response = $this->client->post('users/new/seller?dry-run=1&token=' . self::TOKEN . '&' . http_build_query($this->requestParams));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Seller successfully created', json_decode($response->getBody()->getContents()));
    }

    public function testRemoveUserShouldThrowUnauthorizedWhenNoTokenGiven()
    {
        $response = $this->client->get('users/delete/1');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testRemoveUserShouldThrowUserNotFoundWhenIncorrectIdGiven()
    {
        $response = $this->client->get('users/delete/0?token=' . self::TOKEN);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(self::USER_DOES_NOT_EXIST, json_decode($response->getBody()->getContents()));
    }

    public function testRemoveUserShouldReturnOk()
    {
        $response = $this->client->get('users/delete/1?dry-run=1&token=' . self::TOKEN);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testIsSellerShouldThrowUnauthorizedWhenNoTokenGiven()
    {
        $response = $this->client->get('users/check-seller/1');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(self::AUTHENTICATION_FAILED, json_decode($response->getBody()->getContents()));
    }

    public function testIsSellerShouldThrowSellerNotFound()
    {
        $response = $this->client->get('users/check-seller/0?token=' . self::TOKEN);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains('Seller not found', json_decode($response->getBody()->getContents()));
    }

    public function testIsSellerShouldReturnOkAndTrue()
    {
        $response = $this->client->get('users/check-seller/1?token=' . self::TOKEN);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(json_decode($response->getBody()->getContents()));
    }
}
