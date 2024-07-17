<?php

namespace App\Tests\Helper\Cdn;

use App\Helper\Cdn\CdnHasher;
use PHPUnit\Framework\TestCase;

class CdnHasherTest extends TestCase
{
    public function testAddSignature()
    {
        $data = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];
        $secretKey = 'secret';

        $cdnHasher = new CdnHasher();
        $cdnHasher->addSignature($data, $secretKey);

        $this->assertArrayHasKey('signature', $data);
        $expectedSignature = hash_hmac('sha256', 'param1=value1&param2=value2', $secretKey);
        $this->assertEquals($expectedSignature, $data['signature']);
    }
}
