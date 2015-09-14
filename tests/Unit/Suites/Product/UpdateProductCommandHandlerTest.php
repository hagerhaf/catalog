<?php

namespace LizardsAndPumpkins\Product;

use LizardsAndPumpkins\CommandHandler;
use LizardsAndPumpkins\Queue\Queue;

/**
 * @covers \LizardsAndPumpkins\Product\UpdateProductCommandHandler
 * @uses   \LizardsAndPumpkins\Product\ProductWasUpdatedDomainEvent
 */
class UpdateProductCommandHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Queue|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainEventQueue;

    /**
     * @var UpdateProductCommandHandler
     */
    private $commandHandler;

    protected function setUp()
    {
        $stubProductId = $this->getMock(ProductId::class, [], [], '', false);
        $stubProductSource = $this->getMock(ProductSource::class, [], [], '', false);
        $stubProductSource->method('getId')->willReturn($stubProductId);

        /** @var UpdateProductCommand|\PHPUnit_Framework_MockObject_MockObject $stubCommand */
        $stubCommand = $this->getMock(UpdateProductCommand::class, [], [], '', false);
        $stubCommand->method('getProductSource')->willReturn($stubProductSource);

        $this->mockDomainEventQueue = $this->getMock(Queue::class);

        $this->commandHandler = new UpdateProductCommandHandler($stubCommand, $this->mockDomainEventQueue);
    }

    public function testCommandHandlerInterfaceIsImplemented()
    {
        $this->assertInstanceOf(CommandHandler::class, $this->commandHandler);
    }

    public function testProductWasUpdatedDomainEventIsEmitted()
    {
        $this->mockDomainEventQueue->expects($this->once())->method('add')
            ->with($this->isInstanceOf(ProductWasUpdatedDomainEvent::class));

        $this->commandHandler->process();
    }
}
