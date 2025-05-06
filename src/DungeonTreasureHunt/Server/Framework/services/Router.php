<?php

namespace DungeonTreasureHunt\Framework\services;

class Router
{
    private array $routes = [];

    public function register($uri, $method, $controller): void
    {
        $this->routes[$method][] = [
            'uri' => $uri,
            'controller' => $controller
        ];
    }

    public function getController($uri, $method): ?array
    {
        if (!isset($this->routes[$method])) {
            return null;
        }
        foreach ($this->routes[$method] as $route) {
            $pattern = preg_replace('#\{[^}]+\}#', '([^/]+)', $route['uri']);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $paramsNames = [];

                preg_match_all('#\{([^}]+)\}#', $route['uri'],$paramsNames);
                $paramsNames = $paramsNames[1];

                $params = array_combine($paramsNames, $matches);
                return [$route['controller'], $params];
            }
        }
        return null;
    }
}

