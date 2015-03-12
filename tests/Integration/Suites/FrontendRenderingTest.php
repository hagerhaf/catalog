<?php

namespace Brera;

use Brera\Context\Context;
use Brera\Context\VersionedContext;
use Brera\Http\HttpUrl;
use Brera\DataPool\DataPoolReader;
use Brera\DataPool\KeyValue\InMemory\InMemoryKeyValueStore;
use Brera\DataPool\SearchEngine\InMemorySearchEngine;
use Brera\DataPool\KeyValue\KeyValueStore;

class FrontendRenderingTest extends \PHPUnit_Framework_TestCase
{
    private $sourceId = 333;

    /**
     * @test
     */
    public function itShouldRenderAPageFromAnUrlWithoutVariablesInSnippets()
    {
        $url = HttpUrl::fromString('http://example.com/product1');
        $context = new VersionedContext(DataVersion::fromVersionString('1.0'));
        $snippetKeyGenerator = new GenericSnippetKeyGenerator();
        $urlPathKeyGenerator = new PoCUrlPathKeyGenerator();

        $keyValueStore = new InMemoryKeyValueStore();
        $searchEngine = new InMemorySearchEngine();

        $this->addPageMetaInfoFixtureToKeyValueStorage(
            $keyValueStore,
            $snippetKeyGenerator,
            $urlPathKeyGenerator,
            $context
        );

        $dataPoolReader = new DataPoolReader($keyValueStore, $searchEngine);


        $logger = new InMemoryLogger();

        $pageBuilder = new UrlKeyRequestHandler(
            $url,
            $context,
            $urlPathKeyGenerator,
            $snippetKeyGenerator,
            $dataPoolReader,
            $logger
        );
        $page = $pageBuilder->process();

        $body = $page->getBody();
        $expected = '<html><head><title>Page Title</title></head><body><h1>Headline</h1></body></html>';

        $this->assertEquals($expected, $body);
    }

    /**
     * @param KeyValueStore $keyValueStore
     * @return void
     */
    private function addPageMetaInfoFixtureToKeyValueStorage(
        KeyValueStore $keyValueStore,
        SnippetKeyGenerator $snippetKeyGenerator,
        UrlPathKeyGenerator $urlPathKeyGenerator,
        Context $context
    ) {
        $rootSnippetCode = 'root-snippet';
        $keyValueStore->set(
            $snippetKeyGenerator->getKeyForContext($rootSnippetCode, $this->sourceId, $context),
            '<html><head>{{snippet head}}</head><body>{{snippet body}}</body></html>'
        );
        $pageMetaInfo = PageMetaInfoSnippetContent::create(
            $this->sourceId,
            $rootSnippetCode,
            [$rootSnippetCode, 'head', 'body']
        );

        $urlPathKey = $urlPathKeyGenerator->getUrlKeyForPathInContext('/product1', $context);
        $keyValueStore->set($urlPathKey, json_encode($pageMetaInfo->getInfo()));
        $key = $snippetKeyGenerator->getKeyForContext('head', $this->sourceId, $context);
        $keyValueStore->set($key, '<title>Page Title</title>');
        $key = $snippetKeyGenerator->getKeyForContext('body', $this->sourceId, $context);
        $keyValueStore->set($key, '<h1>Headline</h1>');
    }
}
