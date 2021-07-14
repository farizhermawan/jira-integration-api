<?php

namespace App\Http\Controllers;

use App\Http\RestResponse;

class DefaultController extends Controller
{
  public function hi()
  {
    return RestResponse::message("Hi.");
  }
}
