<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\RestResponse;
use App\Models\Board;

class BoardController extends Controller
{
  public function index()
  {
    return RestResponse::data(Board::getByKey(env('JIRA_BOARD')));
  }
}
