<?php

namespace App\Provider;

interface ProviderAwareInterface
{
    public function setProvider(ProviderInterface $provider): self;
}
