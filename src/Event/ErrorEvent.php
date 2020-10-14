<?php

namespace App\Event;

use Psr\Http\Message\{RequestInterface};
use Throwable;

class ErrorEvent
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
        return implode(' ', [
            'SYSTEM',
            $this->request->getMethod(),
            $this->request->getUri()->__toString(),
            0,
            0,
            json_encode(array_merge(
                [
                    $this->error->getMessage(),
                    "{$this->error->getFile()}:{$this->error->getLine()}"
                ],
                $this->error->getTrace()
            )),
        ]);
    }
}
