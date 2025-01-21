<?php

namespace App\Infrastructure\Controllers;

use App\Infrastructure\Repository\UsersRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    private UsersRepository $users;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->users = $usersRepository;
    }
    public function getUsers(Request $request, Response $response): Response
    {
        $users = $this->users->findAll();
        if(count($users) == 0){
            $response->getBody()->write(json_encode("Users list is empty"));
            return $response->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function addUser(Request $request, Response $response): Response
    {
        $data = $request->getQueryParams();
        $name = htmlspecialchars(trim($data['name'] ?? ""));
        $age = intval($data['age']);

        #проверка данных
        if($name == "" || strlen($name) > 100){

            $response->getBody()->write(json_encode([
                "status" => "error",
                "message" => "Failed to add user. Name must be not empty and low 100 letters",
            ]));

            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');

        }else if(!preg_match('/^([a-z\s]+)$/iu', $name) || count( explode(" ", $name)) > 1){

            $response->getBody()->write(json_encode([
                "status" => "error",
                "message" => "Failed to add user. Name must be one word and contain only letters",
            ]));

            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');

        }else if($age <= 0 || $age > 100){

            $response->getBody()->write(json_encode([
                "status" => "error",
                "message" => "Failed to add user. Age must be more 0 and low 100",
            ]));

            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            $user = $this->users->insert($name, $age);
            $response->getBody()->write(json_encode([
                "status" => "success",
                "message" => "User added successfully",
            ]));
            
        } catch (\Throwable $th) {
            $response->getBody()->write(json_encode([
                "status" => "error",
                "message" => "Failed to add user. Query erors",
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }        

        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }
}