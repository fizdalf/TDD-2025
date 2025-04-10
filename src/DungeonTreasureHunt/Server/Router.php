<?php

namespace DungeonTreasureHunt;

class Router
{
    private array $routes = [];

    public function register($uri, $method, $controller): void
    {
        $this->routes[$method][$uri] = $controller;
    }

    public function getController($uri, $method)
    {
        return $this->routes[$method][$uri] ?? null;
    }
}

