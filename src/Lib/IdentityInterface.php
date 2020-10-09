<?php
namespace App\Lib;

interface IdentityInterface
{
    public function setIdentity(string $id): self;

    public function getIdentity(): string;
}
