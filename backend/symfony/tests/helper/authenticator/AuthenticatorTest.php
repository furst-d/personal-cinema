<?php

namespace App\Tests\Helper\Authenticator;

use App\Helper\Authenticator\Authenticator;
use PHPUnit\Framework\TestCase;

class AuthenticatorTest extends TestCase
{
    private Authenticator $authenticator;

    protected function setUp(): void
    {
        $this->authenticator = new Authenticator();
    }

    public function testGenerateSalt()
    {
        $salt = $this->authenticator->generateSalt();
        $this->assertIsString($salt);
        $this->assertEquals(64, strlen($salt)); // 32 bytes * 2 (hex representation)
    }

    public function testCombinePassword()
    {
        $password = 'testpassword';
        $salt = 'testsalt';
        $hashedPassword = $this->authenticator->combinePassword($password, $salt);

        $this->assertIsString($hashedPassword);
        $this->assertEquals(hash('sha256', $password . $salt), $hashedPassword);
    }

    public function testVerifyPassword()
    {
        $password = 'testpassword';
        $salt = 'testsalt';
        $hashedPassword = hash('sha256', $password . $salt);

        $this->assertTrue($this->authenticator->verifyPassword($password, $hashedPassword, $salt));
        $this->assertFalse($this->authenticator->verifyPassword('wrongpassword', $hashedPassword, $salt));
    }
}

