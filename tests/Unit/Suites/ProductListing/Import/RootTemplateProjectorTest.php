<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Import\RootTemplate\Import;

use LizardsAndPumpkins\DataPool\DataPoolWriter;
use LizardsAndPumpkins\Import\Projector;
use LizardsAndPumpkins\Import\SnippetRendererCollection;

/**
 * @covers \LizardsAndPumpkins\Import\RootTemplate\Import\RootTemplateProjector
 */
class RootTemplateProjectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DataPoolWriter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDataPoolWriter;

    /**
     * @var RootTemplateProjector
     */
    private $projector;

    final protected function setUp()
    {
        /** @var SnippetRendererCollection|\PHPUnit_Framework_MockObject_MockObject $stubSnippetRendererCollection */
        $stubSnippetRendererCollection = $this->createMock(SnippetRendererCollection::class);
        $stubSnippetRendererCollection->method('render')->willReturn([]);

        $this->mockDataPoolWriter = $this->createMock(DataPoolWriter::class);

        $this->projector = new RootTemplateProjector($stubSnippetRendererCollection, $this->mockDataPoolWriter);
    }

    public function testProjectorInterfaceIsImplemented()
    {
        $this->assertInstanceOf(Projector::class, $this->projector);
    }

    public function testSnippetIsWrittenIntoDataPool()
    {
        $projectionSourceDataJson = '{}';

        $this->mockDataPoolWriter->expects($this->once())->method('writeSnippets');

        $this->projector->project($projectionSourceDataJson);
    }
}
