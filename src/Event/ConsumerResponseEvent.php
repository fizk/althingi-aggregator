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
            'request_method' => count($this->request->getHeader('X-HTTP-Method-Override')) > 0
                ? $this->request->getHeader('X-HTTP-Method-Override')[0]
                : $this->request->getMethod(),
            'request_headers' => $this->request->getHeaders(),
            'request_uri' => $this->request->getUri()->__toString(),
            'response_status' => $this->response->getStatusCode(),
            'response_headers' => $this->response->getHeaders(),
            'error_file' => "{$this->exception->getFile()}:{$this->exception->getLine()}",
            'error_message' => $this->exception->getMessage(),
            'error_trace' => $this->exception->getTrace(),
        ]);
    }
}
