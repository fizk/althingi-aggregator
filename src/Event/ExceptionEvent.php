<?php

namespace App\Event;

use Psr\Http\Message\{RequestInterface};
use Throwable;

class ExceptionEvent
{
    private RequestInterface $request;
    private Throwable $error;

    public function __construct(RequestInterface $request, Throwable $error)
    {
        $this->request = $request;
        $this->error = $error;
    }

    public function __toString(): string
    {
        return json_encode([
            'name' => 'system',
            'request_method' => $this->request->getMethod(),
            'request_uri' => $this->request->getUri()->__toString(),
            'response_body' => '',
            'response_status' => 0,
            'error_file' => "{$this->error->getFile()}:{$this->error->getLine()}",
            'error_message' => $this->error->getMessage(),
            'error_trace' => $this->error->getTrace(),
        ]);
    }
}
