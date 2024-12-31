<?php

namespace Konnect\NayaFramework\Lib;

class Route
{
    private static array $routes = [];

    // Add a GET route
    public static function get(string $uri, callable $action): void
    {
        self::$routes['GET'][$uri] = $action;
    }

    // Add a POST route
    public static function post(string $uri, callable $action): void
    {
        self::$routes['POST'][$uri] = $action;
    }

    // Add a PUT route
    public static function put(string $uri, callable $action): void
    {
        self::$routes['PUT'][$uri] = $action;
    }

    // Add a DELETE route
    public static function delete(string $uri, callable $action): void
    {
        self::$routes['DELETE'][$uri] = $action;
    }

    // Register resource routes
    public static function resource(string $uri, string $controller): void
    {
        // Resource routes
        self::get($uri, function () use ($controller) {
            $controllerInstance = new $controller();
            $controllerInstance->index();
        });

        self::get($uri . '/{id}', function ($id) use ($controller) {
            $controllerInstance = new $controller();
            $controllerInstance->show((int)$id);
        });

        self::post($uri, function () use ($controller) {
            $controllerInstance = new $controller();
            $controllerInstance->store();
        });

        self::put($uri . '/{id}', function ($id) use ($controller) {
            $controllerInstance = new $controller();
            $controllerInstance->update((int)$id);
        });

        self::delete($uri . '/{id}', function ($id) use ($controller) {
            $controllerInstance = new $controller();
            $controllerInstance->destroy((int)$id);
        });
    }

    // Dispatch the route based on the request URI and method
    public static function dispatch(): void
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes[$requestMethod] ?? [] as $route => $action) {
            // Replace route placeholders (e.g., /user/{id}) with regex
            $routePattern = preg_replace('/\{(\w+)\}/', '(\w+)', $route);
            $routePattern = '#^' . $routePattern . '$#';

            if (preg_match($routePattern, $requestUri, $matches)) {
                array_shift($matches); // Remove the full match from the array
                call_user_func_array($action, $matches);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    public static function run(): void
    {
        include_once __DIR__ .'../../../routes/web.php';
        self::dispatch();
    }
}
