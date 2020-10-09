<?php
namespace App\Lib;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

interface ClientAwareInterface
{
    public function setHttpClient(ClientInterface $client): self;

    // public function setHttpUri(RequestInterface $uri): self;
}
