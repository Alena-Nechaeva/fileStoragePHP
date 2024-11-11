<?php

namespace Core;

class AuthMiddleware
{
    public static function checkAuthorization(): bool
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            Response::setData(['error' => 'Unauthorized access. Please log in.']);
            return false;
        }

        return true;
    }

    public static function checkAdmin(): bool
    {
        if ($_SESSION['role'] !== 'admin') {
            http_response_code(403);
            Response::setData(['error' => 'Access denied. Admins only.']);
            return false;
        }

        return true;
    }
}