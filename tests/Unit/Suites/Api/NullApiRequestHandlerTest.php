<?php

namespace Brera\Api;

use Brera\Http\HttpRequest;

/**
 * @covers \Brera\Api\NullApiRequestHandler
 * @uses   \Brera\Api\ApiRequestHandler
 */
class NullApiRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NullApiRequestHandler
     */
    private $requestHandler;

    /**
     * @var HttpRequest|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubRequest;

    protected function setUp()
    {
        $this->requestHandler = new NullApiRequestHandler;
        $this->stubRequest = $this->getMock(HttpRequest::class, [], [], '', false);
    }

    public function testApiRequestHandlerIsExtended()
    {
        $this->assertInstanceOf(ApiRequestHandler::class, $this->requestHandler);
    }

    public function testRequestCanNotBeProcessed()
    {
        $this->assertFalse($this->requestHandler->canProcess($this->stubRequest));
    }

    public function testExceptionIsThrownDuringAttemptToProcess()
    {
        $this->setExpectedException(\RuntimeException::class);
        $this->requestHandler->process($this->stubRequest);
    }
}