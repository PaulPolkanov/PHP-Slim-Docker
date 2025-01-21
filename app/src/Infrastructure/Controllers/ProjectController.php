<?php

namespace App\Infrastructure\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProjectController
{
    public function index(Request $request, Response $response): Response
    {
        $className = self::class;
        $response->getBody()->write($className);
        return $response;
    }
}