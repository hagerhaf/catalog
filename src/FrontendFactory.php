<?php

namespace Brera\Poc;

use Brera\Poc\Product\ProductId;

class FrontendFactory implements Factory
{
    use FactoryTrait;

    public function createProductSeoUrlRouter()
    {
        return new ProductSeoUrlRouter(
            $this->getMasterFactory()->createDataPoolReader(),
            $this->getMasterFactory()
        );
    }

    public function createProductDetailPage(ProductId $productId)
    {
        return new ProductDetailHtmlPage(
            $productId,
            $this->getMasterFactory()->createDataPoolReader()
        );
    }
}
