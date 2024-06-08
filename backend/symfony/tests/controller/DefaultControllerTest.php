<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        $expectedPayload = [
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DefaultController.php',
        ];

        $this->assertSame(200, $responseData['code']);
        $this->assertSame('success', $responseData['status']);
        $this->assertArrayHasKey('timestamp', $responseData); // Check if timestamp key exists
        $this->assertSame(1, $responseData['payload']['count']);
        $this->assertSame($expectedPayload, $responseData['payload']['data']);
    }
}
