<?php

namespace App\Event;

use Psr\Http\Message\{ResponseInterface, RequestInterface};
use Throwable;

class ConsumerErrorEvent
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
        return implode(' ', [
            'CONSUMER',
            $this->request->getHeader('X-HTTP-Method-Override')[0],
            $this->request->getUri()->__toString(),
            $this->response->getStatusCode(),
            $this->response->getBody()->getSize(),
            "{$this->error->getFile()}:{$this->error->getLine()} " .
            $this->error->getMessage(),
        ]);
    }
}
