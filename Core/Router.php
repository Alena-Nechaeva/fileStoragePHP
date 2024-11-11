<?php

namespace Core;

use Controllers\UserController;
use Controllers\FileController;
use Controllers\DirController;

class Router
{
    private array $urlList = [
        // all users
        '/registration' => ['POST', [UserController::class, 'register']],
        '/login' => ['POST', [UserController::class, 'login']],
        '/reset-password' => ['POST', [UserController::class, 'resetPassword']],
        '/logout' => ['GET', [UserController::class, 'logout']],
        '/update-me' => ['PUT', [UserController::class, 'updateMe']],
        // admin-only
        '/users' => ['GET', [UserController::class, 'getUsersList']],
        '/users/{id}' => ['GET', [UserController::class, 'getUser']],
        '/users/update/{id}' => ['PUT', [UserController::class, 'updateUser']],
        '/users/delete/{id}' => ['DELETE', [UserController::class, 'deleteUser']],
        // files
        '/files' => ['GET', [FileController::class, 'getFilesList']],
        '/files/{id}' => ['GET', [FileController::class, 'getFile']],
        '/files/add' => ['POST', [FileController::class, 'addFile']],
        '/files/rename' => ['PUT', [FileController::class, 'renameFile']],
        '/files/delete/{id}' => ['DELETE', [FileController::class, 'deleteFile']],
        //folders
        '/directories' => ['GET', [DirController::class, 'getDirectories']],
        '/directories/{id}' => ['GET', [DirController::class, 'getFilesInDirectory']],
        '/directories/add' => ['POST', [DirController::class, 'addDirectory']],
        '/directories/rename' => ['PUT', [DirController::class, 'renameDirectory']],
        '/directories/delete/{id}' => ['DELETE', [DirController::class, 'deleteDirectory']],
        //share files
        '/search-user' => ['POST', [UserController::class, 'searchUserByEmail']],
        '/files/shared-with-me' => ['GET', [FileController::class, 'getSharedFiles']],
        '/files/shared-with-me/{file_id}' => ['GET', [FileController::class, 'getSharedFileById']],
        '/files/shared-with-others/{file_id}' => ['GET', [FileController::class, 'getUsersListWithsAccessToFile']],
        '/files/share/{file_id}/{user_id}' => ['PUT', [fileController::class, 'shareFileWithUser']],
        '/files/remove-access/{file_id}/{user_id}' => ['DELETE', [fileController::class, 'revokeFileAccess']],
        '/files/download/{file_id}' => ['GET', [FileController::class, 'downloadFile']],
    ];

    public function getRoute(): ?array
    {
        $path = $_SERVER['REQUEST_URI'] ?? '';
        $method = $_SERVER['REQUEST_METHOD'] ?? '';

        foreach ($this->urlList as $url => [$expectedMethod, $action]) {
            $pattern = preg_replace('/{\w+}/', '(\d+)', $url);
            if (preg_match("#^$pattern$#", $path, $matches) && $method === $expectedMethod) {

                $params = array_slice($matches, 1);
                return [
                    'controller' => new $action[0](),
                    'method' => $action[1],
                    'methodParams' => $params,
                ];
            }
        }

        return null;
    }
}