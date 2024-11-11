<?php

namespace Controllers;

use Core\AuthMiddleware;
use Core\Request;
use Core\Response;
use Services\UserService;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function register(): void
    {
        $data = Request::getJsonData();
        $result = $this->userService->registerUser($data);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['message' => 'User registered successfully']);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function login(): void
    {
        $data = Request::getJsonData();
        $result = $this->userService->loginUser($data['email'], $data['password']);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['message' => $result['message'], 'user' => $result['user']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function resetPassword(): void
    {
        $data = Request::getJsonData();
        $email = $data['email'] ?? null;

        if (!$email) {
            http_response_code(400);
            Response::setData(['error' => 'Email is required']);
            return;
        }

        $result = $this->userService->sendPasswordResetLink($email);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['message' => $result['message'], 'reset_link' => $result['reset_link']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function logout(): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        session_unset();
        session_destroy();

        setcookie('session_id', '', time() - 14400, '/');

        Response::setData(['message' => 'Logged out successfully']);
    }

    public function updateMe(): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $userId = $_SESSION['user_id'];

        $updatedData = Request::getJsonData();

        $result = $this->userService->updateProfile($userId, $updatedData);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['message' => $result['message']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function getUsersList(): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        if (!AuthMiddleware::checkAdmin()) {
            return;
        }

        $result = $this->userService->getUsersData();

        if ($result['success']) {
            http_response_code(200);
            Response::setData($result['users-list']);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function getUser(int $id): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        if (!AuthMiddleware::checkAdmin()) {
            return;
        }

        $result = $this->userService->getUserById($id);

        if ($result['success']) {
            http_response_code(200);
            Response::setData($result['user']);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function updateUser(int $id): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        if (!AuthMiddleware::checkAdmin()) {
            return;
        }

        $data = Request::getJsonData();

        $result = $this->userService->updateProfile($id, $data);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['message' => $result['message']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function deleteUser(int $id): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        if (!AuthMiddleware::checkAdmin()) {
            return;
        }

        $result = $this->userService->deleteUserById($id);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['message' => $result['message']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }

    public function searchUserByEmail(): void
    {
        if (!AuthMiddleware::checkAuthorization()) {
            return;
        }

        $data = Request::getJsonData();
        $email = $data['email'];

        $result = $this->userService->findUserByEmail($email);

        if ($result['success']) {
            http_response_code(200);
            Response::setData(['user' => $result['user']]);
        } else {
            http_response_code(400);
            Response::setData(['error' => $result['message']]);
        }
    }
}