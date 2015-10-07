<?php


namespace LizardsAndPumpkins\Projection\Catalog\Import;

use LizardsAndPumpkins\Context\Context;

class ConfigurableProductBuilder implements ProductBuilder
{
    /**
     * @var SimpleProductBuilder
     */
    private $simpleProductBuilderDelegate;

    public function __construct(SimpleProductBuilder $simpleProductBuilder)
    {
        $this->simpleProductBuilderDelegate = $simpleProductBuilder;
    }
    
    public function getProductForContext(Context $context)
    {
        // TODO: Implement getProductForContext() method.
    }
}
