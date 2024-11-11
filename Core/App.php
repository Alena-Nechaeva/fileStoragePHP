<?php

namespace Core;

class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function run(): void
    {
        $route = $this->router->getRoute();

        if (!$route) {
            http_response_code(404);
            Response::setData(['error' => 'Route not Found']);
            return;
        }

        $controller = $route['controller'];
        $method = $route['method'];
        $params = $route['methodParams'];

        try {
            $controller->$method(...$params);
        } catch (\Exception $e) {
            http_response_code(500);
            Response::setData(['error' => $e->getMessage()]);
        }
    }
}