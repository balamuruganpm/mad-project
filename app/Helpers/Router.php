<?php
namespace App\Helpers;

class Router {
    private array $routes = [];

    public function add(string $method, string $path, string $controllerAction): void {
        // Convert route like /jobs/edit/{id} into a regular expression
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_\-]+)', $path);
        $pattern = '#^' . $pattern . '$#';
        
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'action' => $controllerAction
        ];
    }

    public function dispatch(string $method, string $uri): void {
        // Remove query string from URI
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }

        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                // Filter matches to get only named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                list($controllerName, $actionName) = explode('@', $route['action']);
                $fullControllerName = "App\\Controllers\\" . $controllerName;

                if (class_exists($fullControllerName)) {
                    $controller = new $fullControllerName();
                    if (method_exists($controller, $actionName)) {
                        call_user_func_array([$controller, $actionName], [$params]);
                        return;
                    }
                }
            }
        }

        // Default: 404 Not Found
        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>The requested route does not exist.</p>";
    }
}
