<?php

namespace App\Models;

use App\Service\JiraService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\Issue
 *
 * @property string $id
 * @property int $ref
 * @property int $sprint_id
 * @property string $type
 * @property string $parent
 * @property string $issue_key
 * @property string $summary
 * @property int|null $original_estimate
 * @property int|null $remaining_estimate
 * @property int|null $time_spent
 * @property string $state
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Issue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Issue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Issue query()
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereIssueKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereOriginalEstimate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereRemainingEstimate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereSprintId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereTimeSpent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Issue whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Issue extends Model
{
  public $incrementing = false;

  protected $hidden = [
    'created_at',
    'updated_at',
  ];

  public static function getIssuesFromJira($sprintId)
  {
    $result = [];
    $jira = new JiraService();
    $jiraIssues = $jira->getIssues($sprintId);
    foreach ($jiraIssues as $jiraIssue) {
      $issue = self::upsert($sprintId, $jiraIssue->id, $jiraIssue);
      $result[] = $issue->id;
    }
    return $result;
  }

  public static function getSubTasksFromJira($uuid)
  {
    $result = [];
    $issue = Issue::whereId($uuid)->firstOrFail();
    $jira = new JiraService();
    $jiraIssue = $jira->getIssue($issue->ref);
    foreach ($jiraIssue->fields->subtasks as $subtask) {
      $subIssue = $jira->getIssue($subtask->id);
      self::upsert($issue->sprint_id, $subtask->id, $subIssue, $issue->issue_key);
      $result[] = $subtask->id;
    }
    return $result;
  }

  /**
   * @param integer $sprintId
   * @param integer $issueId
   * @param \JiraRestApi\Issue\Issue $jiraIssue
   * @param string $parent
   */
  private static function upsert($sprintId, $issueId, $jiraIssue, $parent = null) {
    $issue = Issue::whereSprintId($sprintId)->whereRef($issueId)->first();
    if (!$issue) {
      $issue = new Issue();
      $issue->id = Str::uuid();
    }
    $issue->ref = $issueId;
    $issue->sprint_id = $sprintId;
    $issue->type = $jiraIssue->fields->issuetype->name;
    $issue->parent = $parent == null ? $jiraIssue->key : $parent;
    $issue->issue_key = $jiraIssue->key;
    $issue->summary = $jiraIssue->fields->summary;
    $issue->original_estimate = $jiraIssue->fields->timeTracking->originalEstimateSeconds;
    $issue->remaining_estimate = $jiraIssue->fields->timeTracking->remainingEstimateSeconds;
    $issue->time_spent = $jiraIssue->fields->timeTracking->timeSpentSeconds;
    $issue->state = $jiraIssue->fields->status->name;
    $issue->save();
    return $issue;
  }
}
