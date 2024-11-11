<?php

namespace Core;

class Request
{
  public static function getJsonData(): array
  {
    return json_decode(file_get_contents('php://input'), true) ?? [];
  }

  public static function getFormData(): array
  {
    $formData = [];

    if (!empty($_POST)) {
      $formData['fields'] = $_POST;
    }

    if (!empty($_FILES)) {
      $formData['files'] = $_FILES;
    }

    return $formData;
  }
}