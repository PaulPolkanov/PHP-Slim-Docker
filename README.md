# PHP-Slim-Docker
Development on Slim framework on Docker-compose with PostgreSQL, pgAdmin

## Content
- [Installation with Docker](#installation-with-docker)
- [Installation with Composer](#installation-with-composer)
- [Task 1](#task-1)
- [Task 2](#task-2)
- [Tasks 3 and 4](#tasks-3-and-4)

## Installation with Docker
0. Склонировать репозиторий
```sh
  git clone < https/ssh > < name our dir >
```
1. Задать параметры для подключения к BD в `.env` из `.env.example`
```php
  DB_DRIVER=pgsql
  DB_HOST=db
  DB_PORT=5432
  DB_DATABASE=slim_db
  DB_USERNAME=user
  DB_PASSWORD=password
```
2. Build docker-compose
```sh
  docker-compose build
```
3. Запуск контейнера
```sh
  docker-compose up -d
```
5. Переходим в папку проекта и устанавливаем зависисмости с помощью Composer
```sh
  cd app
  composer install
  cd ..
```
6. Создать таблицу users через Запросник в pgAdmin
```sql
  CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL
  );
```
7. Доступ в приложение и pgAdmin
 - GET [http://localhost:8080/project](http://localhost:8080/project)
 - GET [http://localhost:8080/user/all](http://localhost:8080/user/all)
 - POST [http://localhost:8080/user/add](http://localhost:8080/user/add)
 - pgAdmin [http://localhost:5051](http://localhost:5051)
   
## Installation with Composer
В данном варианте pgAdmin и PostgreSQL нужно использовать внешние

1. Склонировать репозиторий
```sh
  git clone < https/ssh > < name our dir >
```
2. Задать параметры для подключения к BD в `.env` из `.env.example`
```php
  DB_DRIVER=pgsql
  DB_HOST=host_your_bd
  DB_PORT=5432
  DB_DATABASE=slim_db
  DB_USERNAME=user
  DB_PASSWORD=password
```
3. Переходим в папку проекта и устанавливаем зависисмости с помощью Composer
```sh
  сd app
  composer install
```
4. Запустить проект с помощью Composer
```sh
  composer start
```
5. Доступ в приложение
 - GET [http://localhost:8080/project](http://localhost:8080/project)
 - GET [http://localhost:8080/user/all](http://localhost:8080/user/all)
 - POST [http://localhost:8080/user/add](http://localhost:8080/user/add)

## Task 1
1. Создал [ProjectController](https://github.com/PaulPolkanov/PHP-Slim-Docker/blob/master/app/src/Infrastructure/Controllers/ProjectController.php) с методом `index`, который выводит имя класса
   ```php
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
   ```
2. Создал rout `/project` с методом GET в [app/routes.php](https://github.com/PaulPolkanov/PHP-Slim-Docker/blob/master/app/app/routes.php), который вызывает метод `index` `ProjectController`
   ```php
     $app->get('/project', [ProjectController::class, 'index']);
   ```
## Task 2
Подключение к БД PostgreSQL я реализавал немного по совему 

1. Настройки для подключения беруться из `.env` и их возврашщает `Global Settings Object` в файле [app/setting](https://github.com/PaulPolkanov/PHP-Slim-Docker/blob/master/app/app/settings.php)
```php
  'db' => [
      'driver' => $_ENV['DB_DRIVER'] ?? 'pgsql',
      'host' => $_ENV['DB_HOST'] ?? 'db',
      'port' => $_ENV['DB_PORT'] ?? '5432',
      'database' => $_ENV['DB_DATABASE'] ?? 'slim_db',
      'username' => $_ENV['DB_USERNAME'] ?? 'user',
      'password' => $_ENV['DB_PASSWORD'] ?? 'password',
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci',
  ],
```
2. Подключение к БД PostgreSQL с использованием `PDO` реализовано в файле [app/dependencies.php](https://github.com/PaulPolkanov/PHP-Slim-Docker/blob/master/app/app/dependencies.php) 
```php
  PDO::class => function (ContainerInterface $container) {
      $settings = $container->get(SettingsInterface::class);
      $settings = $settings->get('db');
  
      $dsn = "pgsql:host={$settings['host']};port={$settings['port']};dbname={$settings['database']}";
      $pdo = new PDO($dsn, $settings['username'], $settings['password']);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  
      return $pdo;
  },
```
3. Далее я создал [UsersRepository](https://github.com/PaulPolkanov/PHP-Slim-Docker/blob/master/app/src/Infrastructure/Repository/UsersRepository.php)
```php
  namespace App\Infrastructure\Repository;

  use PDO;
  
  class UsersRepository
  {
      private $pdo;
  
      public function __construct(PDO $pdo)
      {
          $this->pdo = $pdo;
      }
  
      public function findAll(): array
      {
          $stmt = $this->pdo->query('SELECT id,name,age FROM users');
          return $stmt->fetchAll();
      }
  
      public function insert(string $name, int $age): bool
      {
          $sql = 'INSERT INTO users (name, age) VALUES (:name, :age)';
          $stmt = $this->pdo->prepare($sql);
          $stmt->bindValue(':name', $name);
          $stmt->bindValue(':age', $age);
          return $stmt->execute();
      }
  }
```
4. Добавил метод `insert()`, который добавляет users в БД
5. Добавил метод `findAll()`, который получает массив users

## Tasks 3 and 4
1. Создаk контроллер [UserController](https://github.com/PaulPolkanov/PHP-Slim-Docker/blob/master/app/src/Infrastructure/Controllers/UserController.php)
```php
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
```
2. Добавил метод `addUser()`, который получает данные, проверяет их и затем записывет в БД через метод `insert()` `UsersRepository`.
3. Добавил метод `getUsers()`, который получает массив users из `UsersRepository`, проверяет , что массив не пустой, и выводит или сообщение `Users list is empty` или массив пользователей в формате json.
4. Создал группу routs для работы с `UserController`
   ```php
    $app->group('/user', function (Group $group) {
        $group->get('/all', [UserController::class, 'getUsers']);
        $group->post('/add', [UserController::class, 'addUser']);
    });
   ```

