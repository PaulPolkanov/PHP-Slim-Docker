<?php

declare(strict_types=1);

use App\Infrastructure\Controllers\ProjectController;
use App\Infrastructure\Controllers\UserController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;



return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/project', [ProjectController::class, 'index']);

    $app->group('/user', function (Group $group) {
        $group->get('/all', [UserController::class, 'getUsers']);
        $group->post('/add', [UserController::class, 'addUser']);
    });

    
};
