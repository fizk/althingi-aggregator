<?php

namespace App\Consumer;

use  App\Consumer\ConsumerInterface;

interface ConsumerAwareInterface
{
    public function setConsumer(ConsumerInterface $consumer): self;
}
