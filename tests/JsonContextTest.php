<?php

declare(strict_types=1);

namespace Tests;

use Alsciende\Behat\JsonContext;
use PHPUnit\Framework\TestCase;

class JsonContextTest extends TestCase
{
    /**
     * @var JsonContext
     */
    private $underTest;

    public function testTheJsonNodeShouldHaveElements()
    {
        $this->loadDataAsJson(['fu' => ['bar', 'baz']]);
        $this->assertNull($this->underTest->theJsonNodeShouldHaveElements('fu', 2));
    }

    public function testTheJsonNodeShouldBeTheString()
    {
        $this->loadDataAsJson(['fu' => ['bar' => 'baz']]);
        $this->assertNull($this->underTest->theJsonNodeShouldBeTheString('fu.bar', 'baz'));
    }

    public function testTheJsonNodeShouldBeFalse()
    {
        $this->loadDataAsJson(['fu' => ['bar' => false]]);
        $this->assertNull($this->underTest->theJsonNodeShouldBeFalse('fu.bar'));
    }

    public function testTheJsonNodeShouldBeTheInteger()
    {
        $this->loadDataAsJson(['fu' => ['bar' => 42]]);
        $this->assertNull($this->underTest->theJsonNodeShouldBeTheInteger('fu.bar', 42));
    }

    public function testTheJsonNodeShouldBeAnArray()
    {
        $this->loadDataAsJson(['fu' => ['bar', 'baz']]);
        $this->assertNull($this->underTest->theJsonNodeShouldBeAnArray('fu'));
    }

    public function testTheJsonNodeShouldBeAnObject()
    {
        $this->loadDataAsJson(['fu' => ['bar' => 'baz']]);
        $this->assertNull($this->underTest->theJsonNodeShouldBeAnObject('fu'));
    }

    public function testTheJsonNodeShouldExist()
    {
        $this->loadDataAsJson(['fu' => ['bar' => 42]]);
        $this->assertNull($this->underTest->theJsonNodeShouldExist('fu.bar'));
    }

    public function testTheJsonNodeShouldNotExist()
    {
        $this->loadDataAsJson(['fu' => ['bar' => 42]]);
        $this->assertNull($this->underTest->theJsonNodeShouldNotExist('fu.baz'));
    }

    public function testTheJsonNodeShouldBeTrue()
    {
        $this->loadDataAsJson(['fu' => ['bar' => true]]);
        $this->assertNull($this->underTest->theJsonNodeShouldBeTrue('fu.bar'));
    }

    public function testTheJsonShouldBeValidAccordingToThisSchema()
    {
        $this->loadDataAsJson(['fu' => ['bar' => true]]);
        $this->assertNull($this->underTest->theJsonShouldBeValidAccordingToThisSchema(
            '{}'
        ));
    }

    public function testTheJsonNodeShouldBeNull()
    {
        $this->loadDataAsJson(['fu' => ['bar' => null]]);
        $this->assertNull($this->underTest->theJsonNodeShouldBeNull('fu.bar'));
    }

    public function testJsonMustBeLoadedFirst()
    {
        $this->expectException(\Exception::class);
        $this->underTest->theJsonNodeShouldBeNull('');
    }

    protected function setUp()
    {
        parent::setUp();
        $this->underTest = new JsonContext();
    }

    private function loadDataAsJson($data)
    {
        $this->underTest->dataStoreContext['json'] = json_decode(json_encode($data));
    }
}
