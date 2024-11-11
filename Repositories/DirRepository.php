<?php

namespace Repositories;

use Core\Db;

class DirRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Db::getConnection();
    }

    public function getDirectoriesByUserId(int $userId): array
    {
        $query = "SELECT id, parent_id, name, created_at FROM directories WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getDirectoryById(int $directoryId): ?array
    {
        $query = "SELECT id, parent_id, name, created_at FROM directories WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $directoryId]);
        $directory = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $directory ?: null;
    }

    public function addDirectory(int $userId, ?int $parentId, string $name): bool
    {
        $query = "INSERT INTO directories (user_id, parent_id, name) VALUES (:user_id, :parent_id, :name)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':user_id' => $userId,
            ':parent_id' => $parentId,
            ':name' => $name
        ]);
    }

    public function renameDirectory(int $directoryId, string $newName): bool
    {
        $query = "UPDATE directories SET name = :new_name WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':new_name' => $newName,
            ':id' => $directoryId
        ]);
    }

    public function getFilesInDirectory(int $directoryId): array
    {
        $query = "SELECT 
            files.id, 
            files.real_name, 
            files.encrypted_name, 
            files.created_at, 
            files.directory_id, 
            directories.name AS directory_name 
        FROM files
        LEFT JOIN directories ON files.directory_id = directories.id
        WHERE files.directory_id = :directory_id
    ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':directory_id' => $directoryId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function deleteDirectory(int $directoryId): bool
    {
        $query = "DELETE FROM files WHERE directory_id = :directory_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':directory_id' => $directoryId]);

        $query = "DELETE FROM directories WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $directoryId]);
    }

    public function directoryExistsInParent(int $userId, ?int $parentId, string $name): bool
    {
        $query = "SELECT COUNT(*) FROM directories WHERE user_id = :user_id AND parent_id = :parent_id AND name = :name";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':user_id' => $userId,
            ':parent_id' => $parentId,
            ':name' => $name
        ]);

        return $stmt->fetchColumn() > 0;
    }
}