<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\RestResponse;
use App\Models\Issue;
use App\Models\Sprint;
use Illuminate\Http\Request;

class IssueController extends Controller
{
  public function index(Request $request)
  {
    $sprintId = $request->get('sprint');
    $sprint = empty($sprintId) ? Sprint::getActiveSprint(env('JIRA_BOARD')) : Sprint::whereId($sprintId)->firstOrFail();
    if ($request->has('sync')) {
      return RestResponse::data(['data' => Issue::getIssuesFromJira($sprint->id)]);
    }

    $issues = Issue::whereSprintId($sprint->id)->whereIn("type", ["Story", "Task", "Bug"])->get();

    $sum = Issue::selectRaw("parent, SUM(original_estimate) as original_estimate, SUM(time_spent) as time_spent, SUM(remaining_estimate) as remaining_estimate")
      ->whereSprintId($sprint->id)->groupBy(["parent"])->get()
      ->groupBy(["parent"]);

    $stats = [];
    foreach ($issues as $issue) {
      $total = $sum[$issue->parent]->first();
      $issue->total_original_estimate = intval($total->original_estimate) ?: 0;
      $issue->total_time_spent = intval($total->time_spent) ?: 0;
      $issue->total_remaining_estimate = intval($total->remaining_estimate) ?: 0;

      if (!isset($stats[$issue->state])) $stats[$issue->state] = [
        "issues" => 0,
        "original_estimate" => 0,
        "time_spent" => 0,
        "remaining_estimate" => 0,
      ];
      $stats[$issue->state]["issues"] += 1;
      $stats[$issue->state]["original_estimate"] += $issue->total_original_estimate;
      $stats[$issue->state]["time_spent"] += $issue->total_time_spent;
      $stats[$issue->state]["remaining_estimate"] += $issue->total_remaining_estimate;
    }
    return RestResponse::data(['stats' => $stats, 'data' => $issues]);
  }

  public function show(Request $request, $issue)
  {
    if ($request->has('sync')) {
      return RestResponse::data(['data' => Issue::getSubTasksFromJira($issue)]);
    }
    return RestResponse::data(Issue::whereId($issue)->first());
  }
}
