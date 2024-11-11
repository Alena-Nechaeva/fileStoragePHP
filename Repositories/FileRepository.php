<?php

namespace Repositories;

use Core\Db;

class FileRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Db::getConnection();
    }

    public function getFilesByUserId(int $userId): array
    {
        $query = "SELECT id, real_name, user_id, encrypted_name, directory_id, created_at FROM files WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getFileById(int $fileId, int $userId): ?array
    {
        $query = "SELECT id, real_name, user_id, encrypted_name, directory_id, created_at FROM files WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id' => $fileId,
            ':user_id' => $userId
        ]);
        $file = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $file ?: null;
    }

    public function addFile(int $userId, ?int $directoryId, string $realName, string $encryptedName): bool
    {
        $query = "INSERT INTO files (user_id, directory_id, real_name, encrypted_name) VALUES (:user_id, :directory_id, :real_name, :encrypted_name)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':user_id' => $userId,
            ':directory_id' => $directoryId,
            ':real_name' => $realName,
            ':encrypted_name' => $encryptedName
        ]);
    }

    public function renameFile(int $fileId, string $newName): bool
    {
        $query = "UPDATE files SET real_name = :new_name WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':new_name' => $newName,
            ':id' => $fileId
        ]);
    }

    public function deleteFile(int $fileId): bool
    {
        $query = "DELETE FROM files WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $fileId]);
    }

    public function isFileSharedWithUser(int $fileId, int $userId): bool
    {
        $query = "SELECT COUNT(*) FROM file_shares WHERE file_id = :file_id AND user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':file_id' => $fileId,
            ':user_id' => $userId
        ]);

        return $stmt->fetchColumn() > 0;
    }

    public function shareFile(int $fileId, int $userId, int $sharedBy): bool
    {
        $query = "INSERT INTO file_shares (file_id, user_id, shared_by) VALUES (:file_id, :user_id, :shared_by)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':file_id' => $fileId,
            ':user_id' => $userId,
            ':shared_by' => $sharedBy
        ]);
    }

    public function getUsersWithAccess(int $fileId): array
    {
        $query = "
        SELECT users.id, users.email, users.name 
        FROM file_shares 
        JOIN users ON file_shares.user_id = users.id 
        WHERE file_shares.file_id = :file_id
    ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':file_id' => $fileId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getFilesSharedWithUser(int $userId): array
    {
        $query = "
            SELECT 
                files.id, 
                files.real_name, 
                files.encrypted_name, 
                users.name AS owner_name 
            FROM file_shares
            JOIN files ON file_shares.file_id = files.id
            JOIN users ON files.user_id = users.id  -- To get the owner's name
            WHERE file_shares.user_id = :user_id
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getSharedFileById(int $fileId, int $userId): ?array
    {
        $query = "
            SELECT 
                files.id, 
                files.real_name, 
                files.encrypted_name, 
                users.name AS owner_name
            FROM file_shares
            JOIN files ON file_shares.file_id = files.id
            JOIN users ON files.user_id = users.id
            WHERE file_shares.file_id = :file_id AND file_shares.user_id = :user_id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':file_id' => $fileId,
            ':user_id' => $userId
        ]);

        $file = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $file ?: null;
    }

    public function removeFileAccess(int $fileId, int $userId): bool
    {
        $query = "DELETE FROM file_shares WHERE file_id = :file_id AND user_id = :user_id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':file_id' => $fileId,
            ':user_id' => $userId
        ]);
    }
}