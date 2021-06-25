<?php

namespace App\Event;

use Psr\Http\Message\{ResponseInterface, RequestInterface};

class ProviderSuccessEvent
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
            'section_name' => 'provider',
            'request_method' => count($this->request->getHeader('X-HTTP-Method-Override')) > 0
                ? $this->request->getHeader('X-HTTP-Method-Override')[0]
                : $this->request->getMethod(),
            'request_headers' => $this->request->getHeaders(),
            'request_uri' => $this->request->getUri()->__toString(),
            'response_status' => $this->response->getStatusCode(),
            'response_headers' => $this->response->getHeaders(),
            'error_file' => null,
            'error_message' => null,
            'error_trace' => null,
        ]);
    }
}
