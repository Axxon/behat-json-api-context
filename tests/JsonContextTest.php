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

    protected function setUp()
    {
        parent::setUp();
        $this->underTest = new JsonContext();
    }

    public function testCreation()
    {
        $this->assertInstanceOf('Alsciende\Behat\JsonContext', $this->underTest);
    }
}
