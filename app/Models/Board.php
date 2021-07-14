<?php

namespace App\Models;

use App\Service\JiraService;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Board
 *
 * @property int $id
 * @property string $board_key
 * @property string $name
 * @property array|null $additional_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Sprint[] $sprints
 * @property-read int|null $sprints_count
 * @method static \Illuminate\Database\Eloquent\Builder|Board newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Board newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Board query()
 * @method static \Illuminate\Database\Eloquent\Builder|Board whereAdditionalData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Board whereBoardKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Board whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Board whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Board whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Board whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Board extends Model
{
  public $incrementing = false;

  protected $hidden = [
    'created_at',
    'updated_at',
  ];

  protected $casts = [
    'additional_data' => 'json'
  ];

  public static function getByKey($key)
  {
    $board = Board::whereBoardKey($key)->first();
    if (!$board) {
      $jira = new JiraService();
      $jiraBoard = $jira->getBoard();
      $board = new Board();
      $board->id = $jiraBoard->id;
      $board->board_key = $key;
      $board->name = $jiraBoard->name;
      $board->additional_data = $jiraBoard->location;
      $board->save();
    }
    return $board;
  }

  public function sprints()
  {
    return $this->hasMany(Sprint::class);
  }
}
