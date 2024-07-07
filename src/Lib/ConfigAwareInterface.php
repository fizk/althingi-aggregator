<?php

namespace App\Lib;

interface ConfigAwareInterface
{
    public function setConfig(array $config): static;
}
