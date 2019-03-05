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
                new Json('{"fu":[{"bar":"baz"}]}'),
                'fu[0].bar'
            )
        );
    }

    public function testEvaluateRootArray()
    {
        $this->assertSame(
            'baz',
            $this->underTest->evaluate(
                new Json('[{"fu":{"bar":"baz"}}]'),
                '[0].fu.bar'
            )
        );
    }
}
