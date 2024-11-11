<?php

namespace Controllers;

use Core\AuthMiddleware;
use Core\Request;
use Core\Response;
use Services\FileService;

class FileController
{
    private FileService $fileService;

    public function __construct()
    {
        $this->fileService = new FileService();
    }

    public function getFilesList(): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $userId = $_SESSION['user_id'];
        $result = $this->fileService->getUserFiles($userId);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['files' => $result['files-list']]);
        } else {
            http_response_code(404);
            Response::setData(['error' => 'Files not found']);
        }
    }

    public function getFile(int $fileId): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $userId = $_SESSION['user_id'];
        $result = $this->fileService->getFileById($fileId, $userId);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['file' => $result['file']]);
        } else {
            http_response_code(404);
            Response::setData(['error' => 'File not found']);
        }
    }

    public function addFile(): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $formData = Request::getFormData();
        $file = $formData['files']['file'] ?? null;
        $directoryId = $formData['fields']['directory_id'] ?? null;


        if (!$file) {
            http_response_code(400);
            Response::setData(['error' => 'No file provided']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $result = $this->fileService->addFileToDb($userId, $directoryId, $file);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['message' => $result['message']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function renameFile(): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $data = Request::getJsonData();
        $fileId = $data['file_id'] ?? null;
        $newName = $data['new_name'] ?? null;

        if (!$fileId || !$newName) {
            http_response_code(400);
            Response::setData(['error' => 'File ID(file_id) and new name(new_name) are required']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $result = $this->fileService->renameFileInDb($fileId, $newName, $userId);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['message' => $result['message']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function deleteFile(int $fileId): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $userId = $_SESSION['user_id'];
        $result = $this->fileService->removeFileFromDb($fileId, $userId);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['message' => $result['message']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function getUsersListWithsAccessToFile(int $fileId): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $result = $this->fileService->getUsersWithFileAccess($fileId);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['users' => $result['users']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function shareFileWithUser(int $fileId, int $userId): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $sharedBy = $_SESSION['user_id'];

        $result = $this->fileService->shareFileWithUser($fileId, $userId, $sharedBy);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['message' => $result['message']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function getSharedFiles(): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $userId = $_SESSION['user_id'];
        $result = $this->fileService->getFilesSharedWithUser($userId);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['files' => $result['files']]);
        } else {
            http_response_code(404);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function getSharedFileById(int $fileId): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $userId = $_SESSION['user_id'];

        $result = $this->fileService->getSharedFileById($fileId, $userId);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['file' => $result['file']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function revokeFileAccess(int $fileId, int $userId): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $result = $this->fileService->revokeFileAccess($fileId, $userId);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['message' => $result['message']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function downloadFile(int $fileId): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $userId = $_SESSION['user_id'];
        $fileOunResp = $this->fileService->getFileById($fileId, $userId);

        if (!$fileOunResp['success']) {
            $fileSharedResp = $this->fileService->getSharedFileById($fileId, $userId);
            if (!$fileSharedResp['success']) {
                http_response_code(403);
                Response::setData(['error' => 'File does not exist or you do not have access to this file']);
                return;
            }
            $file = $fileSharedResp['file'];
        } else {
            $file = $fileOunResp['file'];
        }

        $this->downloadFileFromPath($file['encrypted_name'], $file['real_name']);
    }

    private function downloadFileFromPath(string $encryptedName, string $realName): void
    {
        $filePath = __DIR__ . '/../storage/' . $encryptedName;

        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($realName) . '"');
            header('Content-Length: ' . filesize($filePath));

            readfile($filePath);
            exit;
        } else {
            http_response_code(404);
            Response::setData(['error' => 'File not found on the server']);
        }
    }
}