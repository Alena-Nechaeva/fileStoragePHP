<?php

namespace Repositories;

use Core\Db;

class UserRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Db::getConnection();
    }

    public function saveUser(array $user): bool
    {
        $query = "INSERT INTO users (email, password, role, name) VALUES (:email, :password, :role, :name)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':email' => $user['email'],
            ':password' => $user['password'],
            ':role' => $user['role'],
            ':name' => $user['name']
        ]);
    }

    public function isEmailExists(string $email): bool
    {
        $query = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function isIdExists(int $userId): bool
    {
        $query = "SELECT COUNT(*) FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getUserByEmail(string $email): ?array
    {
        $query = "SELECT id, email, role, password, name FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $user ?: null;
    }
    public function findUserByEmail(string $email): ?array
    {
        $query = "SELECT id, email, name FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function getUsersListFromDb(): array
    {
        $query = "SELECT id, email, role, name FROM users";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getUserFromDb(int $id): ?array
    {
        $query = "SELECT id, email, role, name FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function updateUser(int $userId, array $updatedData): bool
    {
        $query = "UPDATE users SET email = :email, name = :name WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':email' => $updatedData['email'],
            ':name' => $updatedData['name'],
            ':id' => $userId
        ]);
    }

    public function deleteUserFromDb(int $userId): bool
    {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $userId]);
    }
}