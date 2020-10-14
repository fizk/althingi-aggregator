<?php

namespace App\Event;

use Psr\Http\Message\{ResponseInterface, RequestInterface};

class SystemSuccessEvent
{
    private RequestInterface $request;
    private ResponseInterface $response;

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function __toString(): string
    {
        return implode(' ', [
            'SYSTEM',
            $this->request->getMethod(),
            $this->request->getUri()->__toString(),
            $this->response->getStatusCode(),
            $this->response->getBody()->getSize(),
            $this->response->getBody()->__toString()
        ]);
    }
}
