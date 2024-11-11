<?php

namespace Core;

class Response
{
    public static function setHeaders(): void
    {
        header('Content-Type: application/json; charset=utf-8');
    }

    public static function setData($data): void
    {
        self::setHeaders();
        echo json_encode($data);
    }
}