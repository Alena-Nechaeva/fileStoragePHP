<?php

namespace Services;

use Repositories\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function registerUser(array $data): array
    {
        if (empty($data['email']) || empty($data['password']) || empty($data['name'])) {
            return ['success' => false, 'message' => 'Email, password and name are required'];
        }

        if ($this->userRepository->isEmailExists($data['email'])) {
            return ['success' => false, 'message' => 'User with this email already exists'];
        }

        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'The password must consist of 6 or more characters'];
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        $user = [
            'email' => $data['email'],
            'name' => $data['name'],
            'password' => $hashedPassword,
            'role' => 'user'
        ];

        $result = $this->userRepository->saveUser($user);
        return $result ? ['success' => true] : ['success' => false, 'message' => 'Error saving user'];
    }

    public function loginUser(string $email, string $password): array
    {
        $user = $this->userRepository->getUserByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        setcookie('session_id', session_id(), time() + 14400, '/');

        $user = $this->userRepository->getUserFromDb($user['id']);

        return ['success' => true, 'message' => 'Login successful', 'user' => $user];
    }

    public function getUsersData(): array
    {
        $users = $this->userRepository->getUsersListFromDb();
        if (!$users) {
            return ['success' => false, 'message' => 'No users found or an error occurred'];
        }

        return ['success' => true, 'users-list' => $users];
    }

    public function getUserById(int $id): array
    {
        $user = $this->userRepository->getUserFromDb($id);

        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        return ['success' => true, 'user' => $user];
    }

    public function sendPasswordResetLink(string $email): array
    {
        $user = $this->userRepository->getUserByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        $resetLink = "https://example.com/reset_password?token=" . bin2hex(random_bytes(16));

        return ['success' => true, 'message' => 'Password reset link sent', 'reset_link' => $resetLink];
    }

    public function updateProfile(int $userId, array $updatedData): array
    {
        if (!$this->userRepository->isIdExists($userId)) {
            return ['success' => false, 'message' => 'User does not exist'];
        }

        if (!$updatedData || empty($updatedData['email']) || empty($updatedData['name'])) {
            return ['success' => false, 'message' => 'Email and name are required'];
        }

        $success = $this->userRepository->updateUser($userId, $updatedData);

        if ($success) {
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update profile'];
        }
    }

    public function deleteUserById(int $userId): array
    {
        if (!$this->userRepository->isIdExists($userId)) {
            return ['success' => false, 'message' => 'User does not exist'];
        }

        $success = $this->userRepository->deleteUserFromDb($userId);

        if ($success) {
            return ['success' => true, 'message' => 'User deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete user'];
        }
    }

    public function findUserByEmail(string $email): array
    {
        if (!$email) {
            return ['success' => false, 'message' => 'Email is requiered'];
        }

        $user = $this->userRepository->findUserByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        return ['success' => true, 'user' => $user];
    }
}