<?php

declare(strict_types=1);

namespace Tests\Json;

use Alsciende\Behat\Json\Json;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    public function testObject()
    {
        $json = new Json('{"fu":"bar"}');
        $decoded = $json->getDecoded();
        $this->assertIsObject($decoded);
        $this->assertObjectHasAttribute('fu', $decoded);
        $this->assertSame('bar', $decoded->fu);
    }

    public function testArray()
    {
        $json = new Json('["a", "b"]');
        $decoded = $json->getDecoded();
        $this->assertIsArray($decoded);
        $this->assertCount(2, $decoded);
    }

    public function testInteger()
    {
        $json = new Json('42');
        $decoded = $json->getDecoded();
        $this->assertIsInt($decoded);
        $this->assertSame(42, $decoded);
    }

    public function testBoolean()
    {
        $json = new Json('false');
        $decoded = $json->getDecoded();
        $this->assertIsBool($decoded);
        $this->assertSame(false, $decoded);
    }

    public function testNull()
    {
        $json = new Json('null');
        $decoded = $json->getDecoded();
        $this->assertNull($decoded);
    }

    public function testSyntax()
    {
        $this->expectException(\Exception::class);
        new Json("{'fu':'bar'}");
    }
}
