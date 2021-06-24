<?php

namespace App\Event;

use Psr\Http\Message\{ResponseInterface, RequestInterface};
use Throwable;

class ProviderErrorEvent
{
    private RequestInterface $request;
    private ResponseInterface $response;
    private Throwable $error;

    public function __construct(RequestInterface $request, ResponseInterface $response, Throwable $error)
    {
        $this->request = $request;
        $this->response = $response;
        $this->error = $error;
    }

    public function __toString(): string
    {
        return json_encode([
            'name' => 'provider',
            'request_method' => $this->request->getMethod(),
            'request_uri' => $this->request->getUri()->__toString(),
            'response_body' => $this->response->getBody()->__toString(),
            'response_status' => $this->response->getStatusCode(),
            'error_file' => "{$this->error->getFile()}:{$this->error->getLine()}",
            'error_message' => $this->error->getMessage(),
            'error_trace' => $this->error->getTrace(),
        ]);
    }
}
