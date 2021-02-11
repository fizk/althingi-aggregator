<?php

namespace App\Lib\Router;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface as Request;

class RouteCollection implements RouterInterface
{
    protected array $routes;
    protected array $names = [];

    public function __construct()
    {
        $this->routes = [];
    }

    public function addRoute(Route $route): void
    {
        $this->names[$route->getName()] = $route->getPath();
        $this->routes []= $route;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function match(Request $request): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->matches($request)) {
                return $route;
            }
        }

        throw new \Exception("Did not find a matching route. " . (string)$request->getUri(), 405);
    }

    public function generateUri(string $name, array $substitutions = [], array $options = []): string
    {
        if (!array_key_exists($name, $this->names)) {
            throw new InvalidArgumentException('No route name ' . $name, 400);
        }

        $keys = array_map(function ($key) {
            return "{{$key}}";
        }, array_keys($substitutions));
        $values = array_values($substitutions);

        return str_replace($keys, $values, $this->names[$name]);
    }

    public function setRouteConfig(array $config = [])
    {
        foreach ($config as $url => $handler) {
            $this->addRoute(new Route('GET', $url, $handler));
        }
    }
}
