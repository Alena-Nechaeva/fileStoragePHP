<?php

namespace Controllers;

use Core\AuthMiddleware;
use Core\Request;
use Core\Response;
use Services\DirService;

class DirController
{
    private DirService $dirService;

    public function __construct()
    {
        $this->dirService = new DirService();
    }

    public function addDirectory(): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $data = Request::getJsonData();
        $userId = $_SESSION['user_id'];
        $name = $data['name'] ?? null;
        $parentId = $data['parent_id'] ?? null;

        if (!$name) {
            http_response_code(400);
            Response::setData(['error' => 'Directory name is required']);
            return;
        }

        $result = $this->dirService->addDirectoryToDb($userId, $parentId, $name);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['message' => $result['message']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function renameDirectory(): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $data = Request::getJsonData();
        $directoryId = $data['id'] ?? null;
        $newName = $data['new_name'] ?? null;

        if (!$directoryId || !$newName) {
            http_response_code(400);
            Response::setData(['error' => 'Directory ID(id) and new name(new_name) are required']);
            return;
        }

        $result = $this->dirService->renameDirectoryInDb($directoryId, $newName);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['message' => $result['message']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function getFilesInDirectory(int $directoryId): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $result = $this->dirService->getFilesInDirectoryFromDb($directoryId);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['files' => $result['files-list']]);
        } else {
            http_response_code(404);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function getDirectories(): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $userId = $_SESSION['user_id'];
        $result = $this->dirService->getUserDirectoriesFromDB($userId);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['directories' => $result['dir-list']]);
        } else {
            http_response_code(404);
            Response::setData(['error' => 'No directories found']);
        }
    }

    public function deleteDirectory(int $directoryId): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $result = $this->dirService->removeDirectoryFromDb($directoryId);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['message' => $result['message']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

}