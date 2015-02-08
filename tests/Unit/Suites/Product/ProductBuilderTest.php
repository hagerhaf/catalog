<?php

namespace Brera\Product;

/**
 * @covers \Brera\Product\ProductBuilder
 * @uses \Brera\Product\Product
 * @uses \Brera\Product\ProductId
 * @uses \Brera\Product\PoCSku
 * @uses \Brera\XPathParser
 * @uses \Brera\Product\ProductAttribute
 * @uses \Brera\Product\ProductAttributeList
 */
class ProductBuilderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var ProductBuilder
	 */
	private $builder;

	protected function setUp()
	{
		$this->builder = new ProductBuilder();
	}

	/**
	 * @test
	 */
	public function itShouldCreateAProductFromXml()
	{
		$xml = file_get_contents(__DIR__ . '/../../../shared-fixture/product.xml');
		$domDocument = new \DOMDocument();
		$domDocument->loadXML($xml);
		$firstNode = $domDocument->getElementsByTagName('product')->item(0);
		$firstNodeXml = $domDocument->saveXML($firstNode);

		$product = $this->builder->createProductFromXml($firstNodeXml);

		$this->assertInstanceOf(Product::class, $product);
	}

	/**
	 * @test
	 * @expectedException \Brera\Product\InvalidNumberOfSkusPerImportedProductException
	 * @expectedExceptionMessage There must be exactly one SKU in the imported product XML
	 */
	public function itShouldThrowAnExceptionInCaseOfXmlHasNoEssentialData()
	{
		$xml = '<?xml version="1.0"?><node />';
		(new ProductBuilder())->createProductFromXml($xml);
	}
}
