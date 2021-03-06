<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Import\Image;

use LizardsAndPumpkins\Context\DataVersion\DataVersion;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LizardsAndPumpkins\Import\Image\NullProductImageImportCommandFactory
 */
class NullProductImageImportCommandFactoryTest extends TestCase
{
    /**
     * @var NullProductImageImportCommandFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new NullProductImageImportCommandFactory();
    }

    public function testItImplementsTheProductImportCommandFactoryInterface()
    {
        $this->assertInstanceOf(ProductImageImportCommandFactory::class, $this->factory);
    }

    public function testItReturnsNoCommands()
    {
        $stubDataVersion = $this->createMock(DataVersion::class);
        $this->assertSame([], $this->factory->createProductImageImportCommands('image.jpg', $stubDataVersion));
    }
}
