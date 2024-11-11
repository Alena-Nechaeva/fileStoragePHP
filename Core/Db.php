<?php

namespace Core;

use PDO;

class Db
{
  private static $connection = null;

  public static function getConnection(): PDO
  {
    if (self::$connection === null) {
      self::$connection = new PDO('mysql:host=localhost;dbname=cloud-storage', 'root', '');
    }

    return self::$connection;
  }
}