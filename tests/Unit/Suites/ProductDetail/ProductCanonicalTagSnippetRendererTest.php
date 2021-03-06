<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\ProductDetail;

use LizardsAndPumpkins\Context\BaseUrl\BaseUrlBuilder;
use LizardsAndPumpkins\Context\BaseUrl\HttpBaseUrl;
use LizardsAndPumpkins\Context\Context;
use LizardsAndPumpkins\Import\Product\View\ProductView;
use LizardsAndPumpkins\DataPool\KeyValueStore\Snippet;
use LizardsAndPumpkins\DataPool\KeyGenerator\SnippetKeyGenerator;
use LizardsAndPumpkins\Import\SnippetRenderer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LizardsAndPumpkins\ProductDetail\ProductCanonicalTagSnippetRenderer
 * @uses   \LizardsAndPumpkins\Context\BaseUrl\HttpBaseUrl
 * @uses   \LizardsAndPumpkins\DataPool\KeyValueStore\Snippet
 */
class ProductCanonicalTagSnippetRendererTest extends TestCase
{
    /**
     * @var ProductCanonicalTagSnippetRenderer
     */
    private $renderer;

    /**
     * @var SnippetKeyGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubCanonicalTagSnippetKeyGenerator;

    /**
     * @var BaseUrlBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubBaseUrlBuilder;

    /**
     * @var ProductView|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockProductView;

    private function assertContainsSnippetWithKey(string $expectedSnippetKey, Snippet ...$result)
    {
        $found = array_reduce($result, function ($found, Snippet $snippet) use ($expectedSnippetKey) {
            return $found || $snippet->getKey() === $expectedSnippetKey;
        }, false);
        $this->assertTrue($found, sprintf('No snippet with the key "%s" found in Snippet array', $expectedSnippetKey));
    }

    /**
     * @param string $snippetKey
     * @param Snippet[] $result
     * @return Snippet|null
     */
    private function findSnippetByKey($snippetKey, array $result)
    {
        return array_reduce($result, function ($carry, Snippet $snippet) use ($snippetKey) {
            if (isset($carry)) {
                return $carry;
            }
            return $snippet->getKey() === $snippetKey ?
                $snippet :
                null;
        });
    }

    protected function setUp()
    {
        $this->stubCanonicalTagSnippetKeyGenerator = $this->createMock(SnippetKeyGenerator::class);
        $this->stubBaseUrlBuilder = $this->createMock(BaseUrlBuilder::class);
        $this->stubBaseUrlBuilder->method('create')->willReturn(new HttpBaseUrl('https://example.com/'));
        $this->renderer = new ProductCanonicalTagSnippetRenderer(
            $this->stubCanonicalTagSnippetKeyGenerator,
            $this->stubBaseUrlBuilder
        );

        $this->mockProductView = $this->createMock(ProductView::class);
        $this->mockProductView->method('getFirstValueOfAttribute')->willReturn('test.html');
        $this->mockProductView->method('getContext')->willReturn($this->createMock(Context::class));
    }

    public function testImplementsTheSnippetRendererInterface()
    {
        $this->assertInstanceOf(SnippetRenderer::class, $this->renderer);
    }

    public function testReturnsACanonicalTagSnippet()
    {
        $snippetKey = 'canonical_tag';
        $this->stubCanonicalTagSnippetKeyGenerator->method('getKeyForContext')->willReturn($snippetKey);

        $result = $this->renderer->render($this->mockProductView);

        $this->assertInternalType('array', $result);
        $this->assertNotEmpty($result);
        $this->assertContainsOnlyInstancesOf(Snippet::class, $result);
        $this->assertContainsSnippetWithKey($snippetKey, ...$result);

        $snippet = $this->findSnippetByKey($snippetKey, $result);

        $this->assertSame('<link rel="canonical" href="https://example.com/test.html" />', $snippet->getContent());
    }
}
