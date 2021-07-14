<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\RestResponse;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{

  public function index()
  {
    return RestResponse::data(Role::withCount('users')->paginate());
  }

  public function show(Role $role)
  {
    return RestResponse::data($role);
  }

  public function store(Request $request)
  {
    $role = new Role();
    $role->name = $request->name;
    $role->description = $request->description;
    $role->save();
    return RestResponse::created(Role::class);
  }

  public function update(Request $request, Role $role)
  {
    $role->name = $request->name;
    $role->description = $request->description;
    $role->save();
    return RestResponse::updated(Role::class);
  }

  public function destroy(Role $role)
  {
    $role->delete();
    return RestResponse::deleted(Role::class);

  }
}
