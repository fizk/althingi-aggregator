<?php

namespace App\Lib\Router;

use Psr\Http\Message\ServerRequestInterface as Request;

interface RouterInterface
{
    public function addRoute(Route $route): void;

    public function match(Request $request): ?Route;

    public function generateUri(string $name, array $substitutions = [], array $options = []): string;
}
