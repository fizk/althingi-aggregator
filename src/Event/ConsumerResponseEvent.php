<?php

namespace App\Event;

use Psr\Http\Message\{ResponseInterface, RequestInterface};
use Throwable;

class ConsumerResponseEvent
{
    private RequestInterface $request;
    private ResponseInterface $response;
    private ?Throwable $exception = null;

    public function __construct(RequestInterface $request, ResponseInterface $response, ?Throwable $exception = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->exception = $exception;
    }

    public function __toString(): string
    {
        return json_encode([
            'section_name' => 'consumer',
            'request_method' => method_exists($this->request, 'getMethod')
                ? $this->request->getMethod()
                : null,
            'request_headers' => method_exists($this->request, 'getHeaders')
                ? $this->request->getHeaders()
                : [],
            'request_uri' => method_exists($this->request, 'getUri')
                ? (string) $this->request->getUri()
                : '',
            'response_status' => method_exists($this->response, 'getStatusCode')
                ? $this->response->getStatusCode()
                : 0,
            'response_headers' => method_exists($this->response, 'getHeaders')
                ? $this->response->getHeaders()
                : [],
            'error_file' => "{$this->exception?->getFile()}:{$this->exception?->getLine()}",
            'error_message' => $this->exception?->getMessage(),
            'error_trace' => $this->exception?->getTrace(),
        ]);
    }
}
