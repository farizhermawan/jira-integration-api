<?php

namespace App\Http\Controllers;

trait Authenticable
{

  /**
   * @return \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable
   */
  protected function getAuthenticatedUser() {
    return auth()->user();
  }
}
