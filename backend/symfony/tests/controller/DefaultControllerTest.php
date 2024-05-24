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
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'message' => 'Welcome to your new controller!',
                'path' => 'src/Controller/DefaultController.php',
            ]),
            $client->getResponse()->getContent()
        );
    }
}
