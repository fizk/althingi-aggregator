<?php

namespace App\Event;

use Throwable;

class ThrowEvent
{
    private Throwable $error;

    public function __construct(Throwable $error)
    {
        $this->error = $error;
    }

    public function __toString(): string
    {
        return json_encode([
            'name' => 'system',
            'request_method' => 'GET',
            'request_uri' => '/',
            'response_body' => '',
            'response_status' => 0,
            'error_file' => "{$this->error->getFile()}:{$this->error->getLine()}",
            'error_message' => $this->error->getMessage(),
            'error_trace' => $this->error->getTrace(),
        ]);
    }
}
