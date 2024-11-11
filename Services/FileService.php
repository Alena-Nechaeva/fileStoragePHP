<?php

namespace Services;

use Repositories\FileRepository;
use Repositories\DirRepository;

class FileService
{
    private FileRepository $fileRepository;
    private DirRepository $dirRepository;
    private int $maxFileSize = 3 * 1024 * 1024;

    public function __construct()
    {
        $this->fileRepository = new FileRepository();
        $this->dirRepository = new DirRepository();
    }

    public function getUserFiles(int $userId): array
    {
        $files = $this->fileRepository->getFilesByUserId($userId);
        if (!$files) {
            return ['success' => false, 'message' => 'No files found or an error occurred'];
        }

        return ['success' => true, 'files-list' => $files];
    }

    public function getFileById(int $fileId, int $userId): ?array
    {
        $file = $this->fileRepository->getFileById($fileId, $userId);

        if (!$file) {
            return ['success' => false, 'message' => 'File not found'];
        }

        return ['success' => true, 'file' => $file];
    }

    public function addFileToDb(int $userId, ?int $directoryId, array $file): array
    {
        if ($directoryId) {
            $directory = $this->dirRepository->getDirectoryById($directoryId);

            if (!$directory) {
                return ['success' => false, 'message' => 'Directory does not exist'];
            }
        }

        if ($file['size'] > $this->maxFileSize) {
            return ['success' => false, 'message' => 'File size exceeds the 2 MB limit'];
        }

        $realName = $file['name'];
        $encryptedName = bin2hex(random_bytes(16)) . '.' . pathinfo($realName, PATHINFO_EXTENSION);

        $uploadPath = __DIR__ . '/../storage/' . $encryptedName;
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => false, 'message' => 'File upload failed'];
        }

        $this->fileRepository->addFile($userId, $directoryId, $realName, $encryptedName);

        return ['success' => true, 'message' => 'File added successfully'];
    }

    public function renameFileInDb(int $fileId, string $newName, int $userId): array
    {
        $file = $this->fileRepository->getFileById($fileId, $userId);

        if (!$file) {
            return ['success' => false, 'message' => 'File not found'];
        }

        $this->fileRepository->renameFile($fileId, $newName);

        return ['success' => true, 'message' => 'File was renamed successfully'];
    }

    public function removeFileFromDb(int $fileId, int $userId): array
    {
        $file = $this->fileRepository->getFileById($fileId, $userId);

        if (!$file) {
            return ['success' => false, 'message' => 'File not found'];
        }

        $filePath = __DIR__ . '/../storage/' . $file['encrypted_name'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->fileRepository->deleteFile($fileId);

        return ['success' => true, 'message' => 'File was successfully removed'];
    }

    public function shareFileWithUser(int $fileId, int $userId, int $sharedBy): array
    {
        if (!$this->fileRepository->getFileById($fileId, $sharedBy)) {
            return ['success' => false, 'message' => 'File does not exists or user has no access to this file'];
        }

        if ($this->fileRepository->isFileSharedWithUser($fileId, $userId)) {
            return ['success' => false, 'message' => 'User already has access to this file'];
        }

        $this->fileRepository->shareFile($fileId, $userId, $sharedBy);

        return ['success' => true, 'message' => 'File shared successfully'];
    }

    public function getUsersWithFileAccess(int $fileId): array
    {
        $users = $this->fileRepository->getUsersWithAccess($fileId);

        if ($users) {
            return ['success' => true, 'users' => $users];
        } else {
            return ['success' => false, 'message' => "Error retrieving users with file access"];
        }
    }

    public function getFilesSharedWithUser(int $userId): array
    {
        $files = $this->fileRepository->getFilesSharedWithUser($userId);

        return $files ? ['success' => true, 'files' => $files] : ['success' => false, 'message' => "No files shared with this user"];
    }

    public function getSharedFileById(int $fileId, int $userId): array
    {
        if (!$this->fileRepository->isFileSharedWithUser($fileId, $userId)) {
            return ['success' => false, 'message' => 'User does not have access to this file'];
        }

        $file = $this->fileRepository->getSharedFileById($fileId, $userId);

        return $file ? ['success' => true, 'file' => $file] : ['success' => false, 'message' => 'File not found or not shared with this user'];
    }

    public function revokeFileAccess(int $fileId, int $userId): array
    {
        if (!$this->fileRepository->isFileSharedWithUser($fileId, $userId)) {
            return ['success' => false, 'message' => 'User does not have access to this file'];
        }

        $this->fileRepository->removeFileAccess($fileId, $userId);

        return ['success' => true, 'message' => 'File access revoked successfully'];
    }
}