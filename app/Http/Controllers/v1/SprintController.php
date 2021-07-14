<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\RestResponse;
use App\Models\Sprint;
use Illuminate\Http\Request;

class SprintController extends Controller
{
  public function index(Request $request)
  {
    $state = $request->get('state', 'all');
    return RestResponse::data(['data' => Sprint::getAllSprintByKey(env('JIRA_BOARD'), $state, $request->has('sync'))]);
  }

  public function show(Sprint $sprint)
  {
    return RestResponse::data($sprint);
  }
}
