<?php

namespace App\Models;

use App\Service\JiraService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Sprint
 *
 * @property int $id
 * @property int $board_id
 * @property string $name
 * @property string $state
 * @property string $start_date
 * @property string $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Board $board
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Issue[] $issues
 * @property-read int|null $issues_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Worklog[] $worklogs
 * @property-read int|null $worklogs_count
 * @method static \Illuminate\Database\Eloquent\Builder|Sprint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sprint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sprint query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereBoardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Sprint extends Model
{
  public $incrementing = false;

  protected $hidden = [
    'created_at',
    'updated_at',
  ];

  public static function getAllSprintFromJira($boardId)
  {
    $jira = new JiraService();
    $jiraSprints = $jira->getSprints($boardId);

    foreach ($jiraSprints as $jiraSprint) {
      $sprint = Sprint::whereId($jiraSprint->id)->first();
      if (!$sprint) $sprint = new Sprint();
      $sprint->id = $jiraSprint->id;
      $sprint->board_id = $boardId;
      $sprint->name = $jiraSprint->name;
      $sprint->state = $jiraSprint->state;
      $sprint->start_date = Carbon::parse($jiraSprint->startDate)->format("Y-m-d");
      $sprint->end_date = Carbon::parse($jiraSprint->endDate)->format("Y-m-d");
      $sprint->save();
    }
  }

  public static function getAllSprintByKey($key, $state, $sync = false)
  {
    $board = Board::getByKey($key);
    $query = Sprint::whereBoardId($board->id);

    if (!$query->exists()) $sync = true;
    if ($sync) Sprint::getAllSprintFromJira($board->id);
    if ($state != 'all') $query = $query->whereState($state);

    return $query->orderByDesc('id')->get();
  }

  public static function getActiveSprint($key)
  {
    $board = Board::getByKey($key);
    $sprint = Sprint::whereBoardId($board->id)->whereState('active');
    if (!$sprint->exists()) Sprint::getAllSprintFromJira($board->id);
    return $sprint->first();
  }

  public function board()
  {
    return $this->belongsTo(Board::class);
  }

  public function issues()
  {
    return $this->hasMany(Issue::class);
  }

  public function worklogs()
  {
    return $this->hasMany(Worklog::class);
  }
}
