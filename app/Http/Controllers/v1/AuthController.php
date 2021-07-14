<?php

namespace App\Http\Controllers\v1;

use App\Constants\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\RestResponse;
use App\Models\User;
use Exception;
use Laravel\Socialite\Facades\Socialite;
use Redirect;
use Session;

class AuthController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login', 'callback']]);
  }

  public function login($provider)
  {
    $redirect = request()->input('redirect');
    if ($redirect) {
      Session::put('redirect', $redirect);
    }
    if (!$provider || !(in_array($provider, ["google", "yahoo"]))) {
      return RestResponse::badRequest("login provider is not recognized!");
    }
    return Socialite::driver($provider)->stateless()->redirect();
  }

  public function callback($provider)
  {
    $redirect = Session::pull('redirect');
    if (!$provider || !(in_array($provider, ["google", "yahoo"]))) return RestResponse::badRequest("login provider is not recognized!");

    /** @var User $user */
    try {
      $login = Socialite::driver($provider)->stateless()->user();
    } catch (Exception $e) {
      return RestResponse::error($e, HttpStatusCode::BAD_REQUEST);
    }

    $user = User::whereEmail($login->getEmail())->first();
    if (!$user) $user = new User;
    if (empty($user->provider_name)) $user->provider_name = $provider;
    if (empty($user->provider_id)) $user->provider_id = $login->getId();
    if (empty($user->email)) $user->email = $login->getEmail();
    $user->save();

    $token = auth()->login($user);
    return $redirect ? Redirect::to($redirect . "?token={$token}") : $this->respondWithToken($token);
  }

  protected function respondWithToken($token)
  {
    $response = [
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_in' => auth()->factory()->getTTL() * 60
    ];
    return RestResponse::data($response);
  }

  public function me()
  {
    /** @var User $user */
    $user = auth()->user();
    $roles = $user->roles()->get();
    return RestResponse::data(['profile' => $user, 'roles' => $roles->pluck('name')->all()]);
  }

  public function logout()
  {
    auth()->logout();
    return RestResponse::message('Successfully logged out');

  }

  public function refresh()
  {
    return $this->respondWithToken(auth()->refresh());
  }
}
