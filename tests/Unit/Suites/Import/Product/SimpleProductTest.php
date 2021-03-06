<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Import\Product;

use LizardsAndPumpkins\Context\Context;
use LizardsAndPumpkins\Context\DataVersion\DataVersion;
use LizardsAndPumpkins\Import\Product\Exception\ProductAttributeNotFoundException;
use LizardsAndPumpkins\Import\Product\Exception\ProductTypeCodeMismatchException;
use LizardsAndPumpkins\Import\Product\Exception\ProductTypeCodeMissingException;
use LizardsAndPumpkins\Import\Product\Image\ProductImage;
use LizardsAndPumpkins\Import\Product\Image\ProductImageList;
use LizardsAndPumpkins\Import\Tax\ProductTaxClass;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LizardsAndPumpkins\Import\Product\SimpleProduct
 * @covers \LizardsAndPumpkins\Import\Product\RehydrateableProductTrait
 * @uses   \LizardsAndPumpkins\Import\Product\AttributeCode
 * @uses   \LizardsAndPumpkins\Import\Product\ProductAttributeList
 * @uses   \LizardsAndPumpkins\Import\Product\Image\ProductImageList
 * @uses   \LizardsAndPumpkins\Import\Product\ProductId
 * @uses   \LizardsAndPumpkins\Import\Tax\ProductTaxClass
 * @uses   \LizardsAndPumpkins\Context\DataVersion\DataVersion
 * @uses   \LizardsAndPumpkins\Context\SelfContainedContextBuilder
 * @uses   \LizardsAndPumpkins\Context\SelfContainedContext
 */
class SimpleProductTest extends TestCase
{
    /**
     * @var Product
     */
    private $product;

    /**
     * @var ProductId|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubProductId;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubContext;

    /**
     * @var ProductAttributeList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubProductAttributeList;

    /**
     * @var ProductImageList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubProductImages;

    /**
     * @var ProductTaxClass|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubTaxClass;

    public function setUp()
    {
        $this->stubProductId = $this->createMock(ProductId::class);
        $this->stubTaxClass = $this->createMock(ProductTaxClass::class);
        $this->stubProductAttributeList = $this->createMock(ProductAttributeList::class);
        $this->stubContext = $this->createMock(Context::class);
        $this->stubProductImages = $this->createMock(ProductImageList::class);
        $this->product = new SimpleProduct(
            $this->stubProductId,
            $this->stubTaxClass,
            $this->stubProductAttributeList,
            $this->stubProductImages,
            $this->stubContext
        );
    }

    public function testJsonSerializableInterfaceIsImplemented()
    {
        $this->assertInstanceOf(\JsonSerializable::class, $this->product);
    }

    public function testProductIdIsReturned()
    {
        $this->assertSame($this->stubProductId, $this->product->getId());
    }

    public function testItReturnsTheProductTaxClass()
    {
        $this->assertSame($this->stubTaxClass, $this->product->getTaxClass());
    }

    public function testAttributeValueIsReturned()
    {
        $dummyAttributeCode = 'foo';
        $dummyAttributeValue = 'bar';

        $stubProductAttribute = $this->createMock(ProductAttribute::class);
        $stubProductAttribute->method('getValue')->willReturn($dummyAttributeValue);

        $this->stubProductAttributeList->method('hasAttribute')
            ->with($dummyAttributeCode)
            ->willReturn(true);
        $this->stubProductAttributeList->method('getAttributesWithCode')
            ->with($dummyAttributeCode)
            ->willReturn([$stubProductAttribute]);

        $this->assertSame($dummyAttributeValue, $this->product->getFirstValueOfAttribute($dummyAttributeCode));
    }

    public function testAllValuesOfProductAttributeAreReturned()
    {
        $dummyAttributeCode = 'foo';

        $dummyAttributeAValue = 'bar';
        $stubProductAttributeA = $this->createMock(ProductAttribute::class);
        $stubProductAttributeA->method('getValue')->willReturn($dummyAttributeAValue);

        $dummyAttributeBValue = 'baz';
        $stubProductAttributeB = $this->createMock(ProductAttribute::class);
        $stubProductAttributeB->method('getValue')->willReturn($dummyAttributeBValue);

        $this->stubProductAttributeList->method('hasAttribute')
            ->with($dummyAttributeCode)
            ->willReturn(true);
        $this->stubProductAttributeList->method('getAttributesWithCode')
            ->with($dummyAttributeCode)
            ->willReturn([$stubProductAttributeA, $stubProductAttributeB]);

        $expectedValues = [$dummyAttributeAValue, $dummyAttributeBValue];
        $result = $this->product->getAllValuesOfAttribute($dummyAttributeCode);

        $this->assertSame($expectedValues, $result);
    }

    public function testArrayContainingOneEmptyStringIsReturnedIfAttributeIsNotFound()
    {
        $stubProductAttribute = $this->createMock(ProductAttribute::class);
        $stubProductAttribute->method('getValue')->willThrowException(new ProductAttributeNotFoundException);

        $this->stubProductAttributeList->method('getAttributesWithCode')->willReturn([$stubProductAttribute]);

        $result = $this->product->getAllValuesOfAttribute('whatever');

        $this->assertSame([], $result);
    }

    public function testEmptyStringIsReturnedIfAttributeIsNotFound()
    {
        $stubProductAttribute = $this->createMock(ProductAttribute::class);
        $stubProductAttribute->method('getValue')->willThrowException(new ProductAttributeNotFoundException);

        $this->stubProductAttributeList->method('getAttributesWithCode')->willReturn([$stubProductAttribute]);

        $result = $this->product->getFirstValueOfAttribute('whatever');

        $this->assertSame('', $result);
    }

    public function testArrayRepresentationOfProductIsReturned()
    {
        $testProductIdString = 'foo';
        $this->stubProductId->method('__toString')->willReturn($testProductIdString);
        $testTaxClass = 'bar';
        $this->stubTaxClass->method('__toString')->willReturn($testTaxClass);
        $this->stubContext->method('jsonSerialize')->willReturn([]);

        $result = $this->product->jsonSerialize();

        $this->assertInternalType('array', $result);
        $this->assertEquals($testProductIdString, $result['product_id']);
        $this->assertEquals($testTaxClass, $result['tax_class']);
        $this->assertEquals(SimpleProduct::TYPE_CODE, $result['type_code']);
        $this->assertArrayHasKey('attributes', $result);
        $this->assertArrayHasKey('images', $result);
        $this->assertArrayHasKey('context', $result);
    }

    public function testItCanBeCreatedFromAnArray()
    {
        $result = SimpleProduct::fromArray([
            Product::TYPE_KEY => SimpleProduct::TYPE_CODE,
            'product_id' => 'test',
            'tax_class' => 'test tax class',
            'attributes' => [],
            'images' => [],
            'context' => [DataVersion::CONTEXT_CODE => '123']
        ]);
        $this->assertInstanceOf(SimpleProduct::class, $result);
    }

    public function testItThrowsAnExceptionIfTheTypeCodeFieldIsMissingFromSourceArray()
    {
        $allFieldsExceptTypeCode = [
            'product_id' => '',
            'attributes' => [],
            'images' => [],
            'context' => []
        ];
        $this->expectException(ProductTypeCodeMissingException::class);
        $this->expectExceptionMessage(sprintf('The array key "%s" is missing from source array', Product::TYPE_KEY));
        SimpleProduct::fromArray($allFieldsExceptTypeCode);
    }

    /**
     * @param mixed $invalidTypeCode
     * @param string $typeCodeString
     * @dataProvider invalidProductTypeCodeProvider
     */
    public function testItThrowsAnExceptionIfTheTypeCodeInSourceArrayDoesNotMatch($invalidTypeCode, $typeCodeString)
    {
        $this->expectException(ProductTypeCodeMismatchException::class);
        $this->expectExceptionMessage(
            sprintf('Expected the product type code string "simple", got "%s"', $typeCodeString)
        );
        SimpleProduct::fromArray([
            Product::TYPE_KEY => $invalidTypeCode,
            'product_id' => '',
            'attributes' => [],
            'images' => [],
            'context' => []
        ]);
    }

    /**
     * @return array[]
     */
    public function invalidProductTypeCodeProvider() : array
    {
        return [
            ['z1mp3l', 'z1mp3l'],
            [$this, get_class($this)],
            [123, 'integer'],
        ];
    }

    public function testItReturnsTheInjectedContext()
    {
        $this->assertSame($this->stubContext, $this->product->getContext());
    }

    public function testItReturnsTheInjectedProductImages()
    {
        $this->assertSame($this->stubProductImages, $this->product->getImages());
    }

    public function testItReturnsTheNumberOfImages()
    {
        $this->stubProductImages->method('count')->willReturn(3);
        $this->assertSame(3, $this->product->getImageCount());
    }

    public function testItReturnsTheSpecifiedImage()
    {
        $stubImage = $this->createMock(ProductImage::class);
        $this->stubProductImages->method('offsetGet')->with(0)->willReturn($stubImage);
        $this->assertSame($stubImage, $this->product->getImageByNumber(0));
    }

    public function testItReturnsTheGivenProductImageFile()
    {
        $stubImage = $this->createMock(ProductImage::class);
        $stubImage->method('getFileName')->willReturn('test.jpg');
        $this->stubProductImages->method('offsetGet')->with(0)->willReturn($stubImage);
        $this->assertSame('test.jpg', $this->product->getImageFileNameByNumber(0));
        $this->assertSame('test.jpg', $this->product->getMainImageFileName());
    }

    public function testItReturnsTheGivenProductImageLabel()
    {
        $stubImage = $this->createMock(ProductImage::class);
        $stubImage->method('getLabel')->willReturn('Foo bar buz');
        $this->stubProductImages->method('offsetGet')->with(0)->willReturn($stubImage);
        $this->assertSame('Foo bar buz', $this->product->getImageLabelByNumber(0));
        $this->assertSame('Foo bar buz', $this->product->getMainImageLabel());
    }

    public function testItReturnsTrueIfTheProductAttributeIsPresent()
    {
        $dummyAttributeCode = AttributeCode::fromString('test');
        $this->stubProductAttributeList->method('hasAttribute')->with($dummyAttributeCode)->willReturn(true);
        $this->assertTrue($this->product->hasAttribute($dummyAttributeCode));
    }

    public function testItReturnsFalseIfTheProductAttributeIsMissing()
    {
        $dummyAttributeCode = AttributeCode::fromString('test');
        $this->stubProductAttributeList->method('hasAttribute')->with($dummyAttributeCode)->willReturn(false);
        $this->assertFalse($this->product->hasAttribute($dummyAttributeCode));
    }

    public function testItReturnsTheAttributeList()
    {
        $this->assertInstanceOf(ProductAttributeList::class, $this->product->getAttributes());
    }
}
