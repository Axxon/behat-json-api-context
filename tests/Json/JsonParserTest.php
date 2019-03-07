<?php

declare(strict_types=1);

namespace Tests\Json;

use Alsciende\Behat\Json\Json;
use Alsciende\Behat\Json\JsonParser;
use PHPUnit\Framework\TestCase;

class JsonParserTest extends TestCase
{
    /**
     * @var JsonParser
     */
    private $underTest;

    protected function setUp()
    {
        parent::setUp();
        $this->underTest = new JsonParser();
    }

    public function testEvaluateRootObject()
    {
        $this->assertSame(
            'baz',
            $this->underTest->evaluate(
                (new Json('{"fu":[{"bar":"baz"}]}'))->getDecoded(),
                'fu[0].bar'
            )
        );
    }

    public function testEvaluateRootArray()
    {
        $this->assertSame(
            'baz',
            $this->underTest->evaluate(
                (new Json('[{"fu":{"bar":"baz"}}]'))->getDecoded(),
                '[0].fu.bar'
            )
        );
    }

    public function testEvaluateArray()
    {
        $this->assertSame(
            ['bar', 'baz'],
            $this->underTest->evaluate(
                (new Json('{"fu":["bar","baz"]}'))->getDecoded(),
                'fu'
            )
        );
    }

    public function testEvaluateRootAsObject()
    {
        $this->assertEquals(
            (object) ['fu' => ['bar', 'baz']],
            $this->underTest->evaluate(
                (new Json('{"fu":["bar","baz"]}'))->getDecoded()
            )
        );
    }

    public function testEvaluateRootAsArray()
    {
        $this->assertSame(
            ['bar', 'baz'],
            $this->underTest->evaluate(
                (new Json('["bar","baz"]'))->getDecoded()
            )
        );
    }
}
