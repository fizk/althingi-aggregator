<?php

namespace App\Lib;

use Psr\Http\Client\ClientInterface;

interface ClientAwareInterface
{
    public function setHttpClient(ClientInterface $client): static;
}
