<?php

namespace App\Lib\Router;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class Route
{
    protected mixed $handler;
    protected string $path;
    protected string $name;
    protected string $method;
    protected string $pattern;
    protected array $param_keys;
    protected array $matches;

    public function __construct(
        string $method,
        string $path,
        callable |string | RequestHandlerInterface $handler,
        string $name = ''
    ) {
        $this->method = $method;
        $this->path = $path;
        $this->handler = $handler;
        $this->name = $name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHandler(): callable |string| RequestHandlerInterface
    {
        return $this->handler;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttributes(): array
    {
        return $this->matches;
    }

    public function matches(Request $request): bool
    {
        $path = $request->getUri()->getPath();

        $path_has_match = preg_match_all('~^' . $this->getPattern() . '$~', $path, $matches);

        $has_same_method = $request->getMethod() == $this->method;

        if ($path_has_match && $has_same_method) {
            $this->matches = $this->pairKeysWithValues($matches);

            return true;
        }

        return false;
    }

    public function getPattern(): string
    {
        if (!isset($this->pattern)) {
            $this->pattern = preg_replace('~{([A-Za-z0-9_-]*?)}~', '([\.a-zA-Z0-9_-]+)', $this->path);
        }

        return $this->pattern;
    }

    public function getParamKeys(): array
    {
        if (isset($this->param_keys)) {
            return $this->param_keys;
        }

        $match = preg_match_all("~\{([A-Za-z0-9_-]+)\}~", $this->path, $matches);

        $this->param_keys = [];

        if ($match) {
            foreach ($matches[1] as $param) {
                if (in_array($param, $this->param_keys)) {
                    throw new \Exception(
                        "Route parameters should be unique. Please modify the route " .
                        $this->path
                    );
                }

                $this->param_keys [] = $param;
            }
        }

        return $this->param_keys;
    }

    protected function pairKeysWithValues(array $matches): array
    {
        $pairs = [];

        $keys = $this->getParamKeys();

        for ($i = 0; $i < count($keys); $i++) {
            $pairs[$keys[$i]] = $matches[$i + 1][0];
        }

        return $pairs;
    }
}
