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
        return implode(' ', [
            'SYSTEM',
            'GET',
            '/',
            0,
            0,
            json_encode(array_merge(
                [
                    $this->error->getMessage(),
                    "{$this->error->getFile()}:{$this->error->getLine()}",
                ],
                $this->error->getTrace()
            )),
        ]);
    }
}
