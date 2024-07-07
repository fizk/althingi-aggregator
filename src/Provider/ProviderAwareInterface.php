<?php

namespace App\Provider;

interface ProviderAwareInterface
{
    public function setProvider(ProviderInterface $provider): static;
}
