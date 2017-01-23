<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\RestApi;

use LizardsAndPumpkins\Http\ContentDelivery\GenericHttpResponse;
use LizardsAndPumpkins\Http\HttpRequest;
use LizardsAndPumpkins\Http\HttpResponse;
use LizardsAndPumpkins\Http\Routing\HttpRequestHandler;

abstract class ApiRequestHandler implements HttpRequestHandler
{
    final public function process(HttpRequest $request) : HttpResponse
    {
        try {
            $this->processRequest($request);
            $response = $this->getResponse($request);

            return $this->createJsonResponse($response);
        } catch (\Exception $e) {
            /* TODO: Implement error handling */
            throw $e;
        }
    }

    abstract protected function getResponse(HttpRequest $request) : HttpResponse;

    protected function processRequest(HttpRequest $request)
    {
        // Intentionally empty hook method
    }

    private function createJsonResponse(HttpResponse $response): HttpResponse
    {
        $headers = [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Methods' => '*',
            'Content-Type'                 => 'application/json',
        ];

        return GenericHttpResponse::create($response->getBody(), $headers, $response->getStatusCode());
    }
}
