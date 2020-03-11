<?php

namespace Tests\Unit;

use App\Factories\AuthyFactory;
use App\Services\SmsAuthenticator;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $this->assertTrue(true);
        $factory = new AuthyFactory();
        $this->assertInstanceOf(SmsAuthenticator::class, $factory->createAuthenticator('sms'));
    }
}
