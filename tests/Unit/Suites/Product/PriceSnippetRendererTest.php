<?php

namespace LizardsAndPumpkins\Product;

use LizardsAndPumpkins\Context\Context;
use LizardsAndPumpkins\Context\ContextBuilder;
use LizardsAndPumpkins\Context\ContextBuilder\ContextCountry;
use LizardsAndPumpkins\Context\ContextBuilder\ContextWebsite;
use LizardsAndPumpkins\Product\Tax\TaxService;
use LizardsAndPumpkins\Product\Tax\TaxServiceLocator;
use LizardsAndPumpkins\Projection\Catalog\ProductView;
use LizardsAndPumpkins\SnippetKeyGenerator;
use LizardsAndPumpkins\SnippetRenderer;
use LizardsAndPumpkins\SnippetList;
use LizardsAndPumpkins\TaxableCountries;

/**
 * @covers \LizardsAndPumpkins\Product\PriceSnippetRenderer
 * @uses   \LizardsAndPumpkins\Product\Price
 * @uses   \LizardsAndPumpkins\Product\Tax\ProductTaxClass
 * @uses   \LizardsAndPumpkins\Snippet
 * @uses   \LizardsAndPumpkins\SnippetList
 * @uses   \LizardsAndPumpkins\Website\Website
 * @uses   \LizardsAndPumpkins\Country\Country
 */
class PriceSnippetRendererTest extends \PHPUnit_Framework_TestCase
{
    private $testCountries = ['DE', 'UK'];

    /**
     * @var PriceSnippetRenderer
     */
    private $renderer;

    /**
     * @var TaxableCountries|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubTaxableCountries;

    /**
     * @var SnippetKeyGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubSnippetKeyGenerator;

    /**
     * @var ContextBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubContextBuilder;

    /**
     * @var TaxServiceLocator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubTaxServiceLocator;

    /**
     * @var string
     */
    private $testPriceAttributeCode = 'foo';

    /**
     * @return ProductView|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createStubProductView()
    {
        $stubProduct = $this->getMock(Product::class);
        $stubProduct->method('getContext')->willReturn($this->getMock(Context::class));

        $stubProductView = $this->getMock(ProductView::class);
        $stubProductView->method('getOriginalProduct')->willReturn($stubProduct);

        return $stubProductView;
    }

    protected function setUp()
    {
        $stubTaxService = $this->getMock(TaxService::class);
        $stubTaxService->method('applyTo')->willReturnArgument(0);
        $this->stubTaxServiceLocator = $this->getMock(TaxServiceLocator::class);
        $this->stubTaxServiceLocator->method('get')->willReturn($stubTaxService);

        $this->stubTaxableCountries = $this->getMock(TaxableCountries::class);
        $this->stubTaxableCountries->method('getIterator')->willReturn(new \ArrayIterator($this->testCountries));
        $this->stubTaxableCountries->method('getCountries')->willReturn($this->testCountries);
        
        $this->stubSnippetKeyGenerator = $this->getMock(SnippetKeyGenerator::class);
        
        $this->stubContextBuilder = $this->getMock(ContextBuilder::class);
        $this->stubContextBuilder->method('expandContext')->willReturn($this->getMock(Context::class));

        $this->renderer = new PriceSnippetRenderer(
            $this->stubTaxableCountries,
            $this->stubTaxServiceLocator,
            $this->stubSnippetKeyGenerator,
            $this->stubContextBuilder,
            $this->testPriceAttributeCode
        );
    }

    public function testSnippetRendererInterfaceIsImplemented()
    {
        $this->assertInstanceOf(SnippetRenderer::class, $this->renderer);
    }

    public function testItReturnsASnippetList()
    {
        $this->assertInstanceOf(SnippetList::class, $this->renderer->render($this->createStubProductView()));
    }

    public function testNothingIsAddedToSnippetListIfProductDoesNotHaveARequiredAttribute()
    {
        $stubProduct = $this->createStubProductView();
        $stubProduct->method('hasAttribute')->with($this->testPriceAttributeCode)->willReturn(false);

        $snippetList = $this->renderer->render($stubProduct);
        $this->assertCount(0, $snippetList);
    }

    public function testSnippetListContainingSnippetsWithGivenKeyAndPriceIsReturned()
    {
        $dummyPriceSnippetKey = 'bar';
        $dummyPriceAttributeValue = 1;

        $stubProduct = $this->getMock(Product::class);
        $stubProduct->method('getContext')->willReturn($this->getMock(Context::class));
        $stubProduct->method('hasAttribute')->with($this->testPriceAttributeCode)->willReturn(true);
        $stubProduct->method('getFirstValueOfAttribute')
            ->with($this->testPriceAttributeCode)
            ->willReturn($dummyPriceAttributeValue);
        $stubProduct->method('getTaxClass')->willReturn('test class');
        $this->stubContextWebsiteAndCountry($stubProduct);

        /** @var ProductView|\PHPUnit_Framework_MockObject_MockObject $stubProductView */
        $stubProductView = $this->getMock(ProductView::class);
        $stubProductView->method('getOriginalProduct')->willReturn($stubProduct);

        $this->stubSnippetKeyGenerator->method('getKeyForContext')->willReturn($dummyPriceSnippetKey);

        $snippetList = $this->renderer->render($stubProductView);
        $this->assertCount(2, $snippetList);
    }

    /**
     * @param Product|\PHPUnit_Framework_MockObject_MockObject $stubProduct
     */
    private function stubContextWebsiteAndCountry($stubProduct)
    {
        /** @var Context|\PHPUnit_Framework_MockObject_MockObject $stubContext */
        $stubContext = $stubProduct->getContext();
        $stubContext->method('getValue')->willReturnMap([
            [ContextWebsite::CODE, 'test website'],
            [ContextCountry::CODE, 'XX'],
        ]);
    }
}
