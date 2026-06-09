<?php
/**
 * Router Class
 * HumanGrid - Anti-AI Social Media Platform
 */

class Router {
    private $routes = [];

    /**
     * Register a GET route
     */
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    /**
     * Register a POST route
     */
    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    /**
     * Dispatch the request
     */
    public function dispatch() {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Remove base URL if present
        $basePath = '/humangrid/public';
        if (strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }
        
        // Normalize path
        if ($requestUri === '') {
            $requestUri = '/';
        }

        // Check for exact match
        if (isset($this->routes[$method][$requestUri])) {
            $callback = $this->routes[$method][$requestUri];
            return call_user_func($callback);
        }

        // Check for parameterized routes
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // Remove full match
                return call_user_func_array($callback, $matches);
            }
        }

        // 404
        http_response_code(404);
        echo "404 - Page Not Found";
    }
}
