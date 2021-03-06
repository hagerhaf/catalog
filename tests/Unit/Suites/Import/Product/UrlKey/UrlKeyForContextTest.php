<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Import\Product\UrlKey;

use LizardsAndPumpkins\Context\Context;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LizardsAndPumpkins\Import\Product\UrlKey\UrlKeyForContext
 * @uses   \LizardsAndPumpkins\Import\Product\UrlKey\UrlKey
 */
class UrlKeyForContextTest extends TestCase
{
    private $urlKeyType = 'the-type';
    
    /**
     * @var UrlKeyForContext
     */
    private $testUrlKey;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubContext;

    /**
     * @var UrlKeyForContext
     */
    private $urlKeyForContext;

    protected function setUp()
    {
        $this->testUrlKey = UrlKey::fromString('example.html');
        $this->stubContext = $this->createMock(Context::class);
        $this->urlKeyForContext = new UrlKeyForContext($this->testUrlKey, $this->stubContext, $this->urlKeyType);
    }

    public function testItReturnsTheUrlKey()
    {
        $this->assertSame($this->testUrlKey, $this->urlKeyForContext->getUrlKey());
    }

    public function testItReturnsTheContext()
    {
        $this->assertSame($this->stubContext, $this->urlKeyForContext->getContext());
    }

    public function testItReturnsTheUrlKeyString()
    {
        $this->assertSame((string)$this->testUrlKey, (string)$this->urlKeyForContext);
    }

    public function testItDelegatesToTheContextWhenGettingContextValues()
    {
        $this->stubContext->expects($this->once())->method('getValue')->with('test')->willReturn('result');
        $this->assertSame('result', $this->urlKeyForContext->getContextValue('test'));
    }

    public function testItReturnsTheUrlKeyType()
    {
        $this->assertSame($this->urlKeyType, $this->urlKeyForContext->getType());
    }
}
