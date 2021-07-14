<?php

namespace App\Http;

use App\Constants\HttpStatusCode;
use ReflectionClass;

class RestResponse
{
  static function data($data, int $code = HttpStatusCode::OK) {
    return response()->json($data, $code);
  }

  static function message(string $message, int $code = HttpStatusCode::OK) {
    return self::data(["message" => $message], $code);
  }

  static function error(string $message, int $code = HttpStatusCode::SERVER_ERROR) {
    return self::data(["error" => $message, "code" => $code], $code);
  }

  static function badRequest(string $message) {
    return self::error($message, HttpStatusCode::BAD_REQUEST);
  }

  static function conflict(string $message) {
    return self::error($message, HttpStatusCode::CONFLICT);
  }

  static function unauthorized(string $message = "You are not authorized to do this!") {
    return self::error($message, HttpStatusCode::UNAUTHORIZED);
  }

  static function created($entity) {
    $message = sprintf("%s successfully created", self::getEntityName($entity));
    return self::data(["message" => $message, "id" => isset($entity->id) ? $entity->id : ''], HttpStatusCode::CREATED);
  }

  static function updated($entity) {
    return self::message(sprintf("%s successfully updated", self::getEntityName($entity)));
  }

  static function deleted($entity) {
    return self::message(sprintf("%s successfully deleted", self::getEntityName($entity)));
  }

  static function attached($entityParent, $entityChild) {
    return self::message(sprintf("%s successfully attached to this %s", self::getEntityName($entityChild), self::getEntityName($entityParent)), HttpStatusCode::CREATED);
  }

  static function uptached($entityParent, $entityChild) {
    return self::message(sprintf("Detail attached %s for this %s updated", self::getEntityName($entityChild), self::getEntityName($entityParent)));
  }

  static function detached($entityParent, $entityChild) {
    return self::message(sprintf("%s successfully detached from this %s", self::getEntityName($entityChild), self::getEntityName($entityParent)));
  }

  private static function getEntityName($entity) {
    try {
      $reflection = new ReflectionClass($entity);
      return $reflection->getShortName();
    } catch (\ReflectionException $e) {
      return is_string($entity) ? $entity : "Unknown";
    }
  }
}
