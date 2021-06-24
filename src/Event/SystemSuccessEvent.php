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
        return json_encode([
            'name' => 'system',
            'request_method' => $this->request->getMethod(),
            'request_uri' => $this->request->getUri()->__toString(),
            'response_body' => $this->response->getBody()->__toString(),
            'response_status' => $this->response->getStatusCode(),
            'error_file' => null,
            'error_message' => null,
            'error_trace' => null,
        ]);
    }
}
