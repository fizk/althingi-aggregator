<?php

namespace App\Lib\Router;

use Closure;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Holds instances of the Route class
 */
class RouteCollection implements RouterInterface
{
    /**
     * Array which contains instances of the Route class
     *
     * @var Route[]
     */
    protected $routes;
    protected $names = [];

    /**
     * Creates a new instance of the RouteCollection class
     *
     * @return void
     */
    public function __construct()
    {
        $this->routes = [];
    }

    /**
     * Adds an instance of the Route class to the routes property
     *
     * @param Route $route
     * @return void
     */
    public function addRoute(Route $route): void
    {
        $this->names[$route->getName()] = $route->getPath();
        $this->routes []= $route;
    }

    /** Gets the routes property
     *
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Finds an instance of the Route class which matches the given request
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return Route
     * @throws \Exception if no matching Route instance is found
     */
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
