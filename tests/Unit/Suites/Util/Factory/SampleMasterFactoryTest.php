<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Util\Factory;

use LizardsAndPumpkins\Util\Factory\Exception\UndefinedFactoryMethodException;

/**
 * @covers \LizardsAndPumpkins\Util\Factory\SampleMasterFactory
 * @covers \LizardsAndPumpkins\Util\Factory\MasterFactoryTrait
 * @uses   \LizardsAndPumpkins\Util\Factory\StubFactory
 */
class SampleMasterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SampleMasterFactory
     */
    private $sampleMasterFactory;

    /**
     * @var StubFactory
     */
    private $stubFactory;

    protected function setUp()
    {
        $this->sampleMasterFactory = new SampleMasterFactory;
        $this->stubFactory = new StubFactory;
        $this->sampleMasterFactory->register($this->stubFactory);
    }

    public function testMasterFactoryIsSetOnChildFactory()
    {
        $this->assertAttributeEquals($this->sampleMasterFactory, 'masterFactory', $this->stubFactory);
    }

    public function testOnlyPublicFactoryMethodsStartingWithGetOrCreateAreRegisteredOnMasterFactory()
    {
        $expectedMethods = ['createSomething' => $this->stubFactory, 'getSomething' => $this->stubFactory];
        $this->assertAttributeSame($expectedMethods, 'methods', $this->sampleMasterFactory);
    }

    public function testExceptionIsThrownDuringAttemptToCallNotRegisteredFactoryMethod()
    {
        $this->expectException(UndefinedFactoryMethodException::class);
        $this->sampleMasterFactory->nonRegisteredMethod();
    }

    public function testRegisteredFactoryMethodsCanBeCalled()
    {
        $parameter = 'foo';
        $result = $this->sampleMasterFactory->createSomething($parameter);

        $this->assertSame($parameter, $result);
    }

    public function testCallsACallbackMethodOnRegisteredFactoryWithACallback()
    {
        /** @var FactoryWithCallback|Factory|\PHPUnit_Framework_MockObject_MockObject $mockFactoryWithCallback */
        $mockFactoryWithCallback = $this->getMockBuilder([Factory::class, FactoryWithCallback::class])->getMock();
        $mockFactoryWithCallback->expects($this->once())->method('factoryRegistrationCallback')
            ->with($this->sampleMasterFactory);

        $this->sampleMasterFactory->register($mockFactoryWithCallback);
    }
}
