<?php
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