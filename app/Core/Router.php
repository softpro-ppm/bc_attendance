<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $notFoundHandler;

    public function addRoute($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put($path, $handler)
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete($path, $handler)
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function notFound($handler)
    {
        $this->notFoundHandler = $handler;
    }

    public function dispatch($method, $uri)
    {
        // Remove query string
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Remove trailing slash
        $uri = rtrim($uri, '/');
        
        // If empty URI, set to root
        if (empty($uri)) {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri)) {
                $params = $this->extractParams($route['path'], $uri);
                return $this->executeHandler($route['handler'], $params);
            }
        }

        // Route not found
        if ($this->notFoundHandler) {
            return $this->executeHandler($this->notFoundHandler, []);
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    private function matchPath($routePath, $uri)
    {
        // Convert route path to regex pattern
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $uri);
    }

    private function extractParams($routePath, $uri)
    {
        $params = [];
        
        // Extract parameter names from route path
        preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames);
        
        // Convert route path to regex pattern
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        // Extract parameter values from URI
        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Remove full match
            foreach ($paramNames[1] as $index => $name) {
                if (isset($matches[$index])) {
                    $params[$name] = $matches[$index];
                }
            }
        }
        
        return $params;
    }

    private function executeHandler($handler, $params)
    {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }
        
        if (is_string($handler)) {
            $parts = explode('@', $handler);
            if (count($parts) === 2) {
                $controllerClass = 'App\\Controllers\\' . $parts[0];
                $method = $parts[1];
                
                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $method)) {
                        return call_user_func_array([$controller, $method], $params);
                    }
                }
            }
        }
        
        throw new \Exception('Invalid route handler');
    }
}
