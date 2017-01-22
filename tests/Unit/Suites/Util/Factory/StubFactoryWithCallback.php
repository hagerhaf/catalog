<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Util\Factory;

class StubFactoryWithCallback implements Factory, FactoryWithCallback
{
    /**
     * @var MasterFactory
     */
    private $masterFactory;

    public function setMasterFactory(MasterFactory $masterFactory)
    {
        $this->masterFactory = $masterFactory;
    }

    public function factoryRegistrationCallback(MasterFactory $masterFactory)
    {
        // Intentionally empty
    }
}
