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
            'section_name' => 'provider',
            'request_method' => count($this->request->getHeader('X-HTTP-Method-Override')) > 0
                ? $this->request->getHeader('X-HTTP-Method-Override')[0]
                : $this->request->getMethod(),
            'request_headers' => $this->request->getHeaders(),
            'request_uri' => $this->request->getUri()->__toString(),
            'response_status' => $this->response->getStatusCode(),
            'response_headers' => $this->response->getHeaders(),
            'error_file' => "{$this->error->getFile()}:{$this->error->getLine()}",
            'error_message' => $this->error->getMessage(),
            'error_trace' => $this->error->getTrace(),
        ]);
    }
}
