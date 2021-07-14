<?php

namespace App\Models;

trait Auditable
{
  protected static function boot()
  {
    parent::boot();
    /* @var $auth User */
    $auth = auth()->user();
    $auditor = $auth == null ? "system" : "{$auth->email}";
    static::saving(function ($entity) use ($auditor) {
      if (!$entity->exists) $entity->created_by = $auditor;
      else $entity->updated_by = $auditor;
    });
  }
}
