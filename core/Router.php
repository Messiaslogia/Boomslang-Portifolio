<?php

class Router
{
    private $routes = [];

    function add($method, $pattern, $handler)
    {
        // transforma /users/{id} em regex, captura nomes
        $names = [];
        $regex = preg_replace_callback('#\{([^}]+)\}#', function ($m) use (&$names) {
            $names[] = $m[1];
            return '([^/]+)';
        }, $pattern);
        $regex = '#^' . rtrim($regex, '/') . '/?$#'; // aceita com/sem barra final
        $this->routes[] = compact('method', 'pattern', 'regex', 'names', 'handler');
    }

    function get($patterns, $handler)
    {
        $this->add('GET', $patterns, $handler);
    }

    function post($patterns, $handler)
    {
        $this->add('POST', $patterns, $handler);
    }

    public function dispatch($method, $path)
    {
        $path = rtrim($path, '/');
        if ($path === '')
            $path = '/';
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method)
                continue;
            if (preg_match($route['regex'], $path, $matches)) {
                array_shift($matches); // remove match completo
                $params = [];
                foreach ($route['names'] as $i => $name) {
                    $params[$name] = isset($matches[$i]) ? $matches[$i] : null;
                }
                $handler = $route['handler'];
                // se handler for callable, chama com params (ou sem)
                if (is_callable($handler)) {
                    return $handler($params);
                } else {
                    // se handler for string caminho para arquivo
                    return require $handler;
                }
            }
        }
        http_response_code(404);
        echo "404 - Página não encontrada";
    }
}