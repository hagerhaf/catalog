<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Import\RootTemplate\Import;

use LizardsAndPumpkins\DataPool\DataPoolWriter;
use LizardsAndPumpkins\Import\Projector;
use LizardsAndPumpkins\Import\SnippetRendererCollection;

class RootTemplateProjector implements Projector
{
    /**
     * @var SnippetRendererCollection
     */
    private $snippetRendererCollection;

    /**
     * @var DataPoolWriter
     */
    private $dataPoolWriter;
    
    public function __construct(
        SnippetRendererCollection $snippetRendererCollection,
        DataPoolWriter $dataPoolWriter
    ) {
        $this->snippetRendererCollection = $snippetRendererCollection;
        $this->dataPoolWriter = $dataPoolWriter;
    }

    /**
     * @param mixed $productsPerPageSourceData
     */
    public function project($productsPerPageSourceData)
    {
        $projectionData = [];
        $snippets = $this->snippetRendererCollection->render($projectionData);
        $this->dataPoolWriter->writeSnippets(...$snippets);
    }
}
