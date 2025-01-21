# PHP-Slim-Docker
Development on Slim framework on Docker-compose with PostgreSQL, pgAdmin

## Content
- [Installation with Docker](#installation-with-docker)
- [Installation with Composer](#installation-with-composer)
- [Description](#description-project)

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
5. Установить зависисмости с помощью Composer
```sh
  composer install
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
3. Установить зависисмости с помощью Composer
```sh
  composer install
```
4. Запустить проект с помощью Composer
```sh
  composer start
```
5. Доступ в приложение и pgAdmin
 - GET [http://localhost:8080/project](http://localhost:8080/project)
 - GET [http://localhost:8080/user/all](http://localhost:8080/user/all)
 - POST [http://localhost:8080/user/add](http://localhost:8080/user/add)

## Task 1

## Task 2

## Task 3

## Task 4

