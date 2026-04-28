<?php

require_once __DIR__ . '/vendor/autoload.php';

$routes = require __DIR__ . '/src/Interface/Http/routes.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

foreach ($routes as $route) {
    if ($route['method'] === $method && $route['path'] === $uri) {
        header('Content-Type: application/json');
        echo json_encode($route['action']());
        exit;
    }
}

http_response_code(404);
echo json_encode([
    'success' => false,
    'error' => 'Not Found'
]);