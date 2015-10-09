<?php

namespace LizardsAndPumpkins\Image;

/**
 * @covers \LizardsAndPumpkins\Image\ImageWasAddedDomainEventHandler
 */
class ImageWasAddedDomainEventHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ImageWasAddedDomainEventHandler
     */
    private $handler;

    /**
     * @var ImageWasAddedDomainEvent|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockImageWasAddedDomainEvent;

    /**
     * @var ImageProcessorCollection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockImageProcessorCollection;

    protected function setUp()
    {
        $this->mockImageWasAddedDomainEvent = $this->getMock(ImageWasAddedDomainEvent::class, [], [], '', false);
        $this->mockImageProcessorCollection = $this->getMock(ImageProcessorCollection::class, [], [], '', false);

        $this->handler = new ImageWasAddedDomainEventHandler(
            $this->mockImageWasAddedDomainEvent,
            $this->mockImageProcessorCollection
        );
    }

    public function testImageDomainEventHandlerIsReturned()
    {
        $this->assertInstanceOf(ImageWasAddedDomainEventHandler::class, $this->handler);
    }

    public function testAllImagesArePassedThroughImageProcessor()
    {
        $imageFilename = 'test_image.jpg';
        $this->mockImageWasAddedDomainEvent->method('getImageFileName')->willReturn($imageFilename);

        $this->mockImageProcessorCollection->expects($this->once())->method('process');

        $this->handler->process();
    }
}