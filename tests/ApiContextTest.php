<?php

declare(strict_types=1);

namespace Tests;

use Alsciende\Behat\ApiContext;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\Client;

class ApiContextTest extends TestCase
{
    /**
     * @var ApiContext
     */
    private $underTest;

    protected function setUp()
    {
        parent::setUp();
        $client = $this->createMock(Client::class);
        $this->underTest = new ApiContext($client);
    }

    public function testCreation()
    {
        $this->assertInstanceOf('Alsciende\Behat\ApiContext', $this->underTest);
    }
}
