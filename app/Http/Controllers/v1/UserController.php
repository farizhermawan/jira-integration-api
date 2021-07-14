<?php

namespace App\Http\Controllers\v1;

use App\Constants\DefaultValue;
use App\Http\Controllers\Controller;
use App\Http\RestResponse;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function index(Request $request)
  {
    $filter = $request->input('filter');
    $query = $request->input('q');
    $perPage = $request->input('limit', DefaultValue::PER_PAGE);
    $builder = User::orderBy('first_name');
    if (!empty($filter) && !empty($query)) $builder = $builder->where($filter, 'LIKE', "%$query%");
    if ($filter != "membership_id") $builder = $builder->whereNotNull('membership_id');
    return $perPage == -1
      ? RestResponse::data(['data' => $builder->get()])
      : RestResponse::data($builder->paginate($perPage));
  }

  public function show(User $user)
  {
    return RestResponse::data($user);
  }

  public function update(Request $request, User $user)
  {
    $input = $request->all();
    $user->update($input);
    $user->save();
    return RestResponse::updated(User::class);
  }
}
