# PHP-Slim-Docker
Development on Slim framework on Docker-compose with PostgreSQL, pgAdmin

## Content
- [Installation](#installation)
- [Description](#description-project)

## Installation
0. Склонировать репозиторий
```sh
  git clone < https/ssh > < name our dir >
```
1. Задать параметры для BD в `.env` из `.env.example`
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
4. Создать таблицу users через Запросник в pgAdmin
```sql
  CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL
  );
```
5. Доступ в приложение
 - GET [http://localhost:8080/project](http://localhost:8080/project)
 - GET [http://localhost:8080/user/all](http://localhost:8080/user/all)
 - POST [http://localhost:8080/user/add](http://localhost:8080/user/add)
  
```json
{
  "name": "example_name"
  "age": 12
}
```

## Description project

