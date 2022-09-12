<?php

namespace App\Lib;

use Psr\Log\LoggerInterface;

interface LoggerAwareInterface
{
    public function setLogger(LoggerInterface $logger): self;
}
