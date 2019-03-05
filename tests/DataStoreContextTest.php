<?php

declare(strict_types=1);

namespace Tests;

use Alsciende\Behat\DataStoreContext;
use PHPUnit\Framework\TestCase;

class DataStoreContextTest extends TestCase
{
    /**
     * @var DataStoreContext
     */
    private $underTest;

    protected function setUp()
    {
        parent::setUp();
        $this->underTest = new DataStoreContext();
    }

    public function testEvaluateArrayIndex()
    {
        $this->underTest['fu'] = 'bar';
        $this->assertSame('Hello bar', $this->underTest->evaluate('Hello -< [fu] >-'));
    }

    public function testEvaluateObjectProperty()
    {
        $this->underTest['fu'] = (object) ['bar' => 'baz'];
        $this->assertSame('Hello baz', $this->underTest->evaluate('Hello -< [fu].bar >-'));
    }
}
