<?php

namespace LizardsAndPumpkins\Projection\Catalog\Import;

use LizardsAndPumpkins\Context\Context;
use LizardsAndPumpkins\Product\Product;

interface ProductBuilder
{
    /**
     * @param Context $context
     * @return Product
     */
    public function getProductForContext(Context $context);
}
