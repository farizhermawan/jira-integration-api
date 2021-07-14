<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Worklog
 *
 * @property int $id
 * @property string $issue_id
 * @property int $sprint_id
 * @property int $user_id
 * @property string $parent
 * @property string $issue_key
 * @property string $date
 * @property int $time_spent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Issue $issue
 * @property-read \App\Models\Sprint $sprint
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Worklog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Worklog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Worklog query()
 * @method static \Illuminate\Database\Eloquent\Builder|Worklog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worklog whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worklog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worklog whereIssueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worklog whereIssueKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worklog whereParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worklog whereSprintId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worklog whereTimeSpent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worklog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Worklog whereUserId($value)
 * @mixin \Eloquent
 */
class Worklog extends Model
{
  protected $hidden = [
    'created_at',
    'updated_at',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function sprint()
  {
    return $this->belongsTo(Sprint::class);
  }

  public function issue()
  {
    return $this->belongsTo(Issue::class);
  }
}
