<?php

namespace App\Lib;

interface IdentityInterface
{
    public function setIdentity(string $id): static;

    public function getIdentity(): string;
}
