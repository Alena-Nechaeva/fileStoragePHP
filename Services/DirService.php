<?php

namespace Services;

use Repositories\DirRepository;

class DirService
{
    private DirRepository $dirRepository;

    public function __construct()
    {
        $this->dirRepository = new DirRepository();
    }

    public function addDirectoryToDb(int $userId, ?int $parentId, string $name): array
    {
        if ($parentId) {
            $directory = $this->dirRepository->getDirectoryById($parentId);

            if (!$directory) {
                return ['success' => false, 'message' => 'Parent directory does not exist'];
            }
        }

        if ($this->dirRepository->directoryExistsInParent($userId, $parentId, $name)) {
            return ['success' => false, 'message' => 'A directory with this name already exists in the parent directory'];
        }

        $result = $this->dirRepository->addDirectory($userId, $parentId, $name);
        return $result ? ['success' => true, 'message' => 'Directory created successfully'] : ['success' => false, 'message' => 'Failed to add directory'];
    }

    public function renameDirectoryInDb(int $directoryId, string $newName): array
    {
        $directory = $this->dirRepository->getDirectoryById($directoryId);

        if (!$directory) {
            return ['success' => false, 'message' => 'Directory not found'];
        }

        $this->dirRepository->renameDirectory($directoryId, $newName);

        return ['success' => true, 'message' => 'Directory renamed successfully'];
    }

    public function getUserDirectoriesFromDB(int $userId): array
    {
        $folders = $this->dirRepository->getDirectoriesByUserId($userId);
        if (empty($folders)) {
            return ['success' => false, 'message' => 'No directories found or an error occurred'];
        }

        return ['success' => true, 'dir-list' => $folders];
    }

    public function getFilesInDirectoryFromDb(int $directoryId): array
    {
        $files = $this->dirRepository->getFilesInDirectory($directoryId);

        if (!$files) {
            return ['success' => false, 'message' => 'No files found or an error occurred'];
        }

        return ['success' => true, 'files-list' => $files];
    }

    public function removeDirectoryFromDb(int $directoryId): array
    {
        $directory = $this->dirRepository->getDirectoryById($directoryId);

        if (!$directory) {
            return ['success' => false, 'message' => 'Directory not found'];
        }

        $this->dirRepository->deleteDirectory($directoryId);

        return ['success' => true, 'message' => 'Directory deleted successfully'];
    }
}