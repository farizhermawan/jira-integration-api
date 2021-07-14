<?php

namespace App\Service;

use InvalidArgumentException;
use JiraRestApi\Board\BoardService;
use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Sprint\SprintService;

class JiraService
{
  private $config;

  private $board;
  private $sprint;

  private $boardService = null;
  private $sprintService = null;
  private $issueService = null;

  public function __construct()
  {
    $this->config = new ArrayConfiguration([
      'jiraHost' => env('JIRA_HOST'),
      'jiraUser' => env('JIRA_USER'),
      'jiraPassword' => env('JIRA_PASS'),
    ]);

    $results = $this->getBoardService()->getBoardList(['name' => env('JIRA_BOARD')]);
    if (count($results) == 0) throw new InvalidArgumentException(env('JIRA_BOARD') . " is not found");

    $this->board = $results[0];
    $results = $this->getBoardService()->getBoardSprints($this->board->id, ['state' => 'active']);
    if (count($results) > 0) $this->sprint = $results[0];
  }

  private function getBoardService()
  {
    if ($this->boardService == null) $this->boardService = new BoardService($this->config);
    return $this->boardService;
  }

  private function getSprintService()
  {
    if ($this->sprintService == null) $this->sprintService = new SprintService($this->config);
    return $this->sprintService;
  }

  private function getIssueService()
  {
    if ($this->issueService == null) $this->issueService = new IssueService($this->config);
    return $this->issueService;
  }

  public function getBoard()
  {
    return $this->board;
  }

  public function getActiveSprint()
  {
    return $this->sprint;
  }

  public function getSprints($boardId = null)
  {
    if (empty($boardId)) $boardId = $this->board->id;
    return $this->getBoardService()->getBoardSprints($boardId);
  }

  public function getIssues($sprintId = null)
  {
    if (empty($sprintId)) $sprintId = $this->sprint->id;
    $params = [
      "jql" => urlencode('type IN ("Story", "Task", "Bug")'),
      "fields" => "summary,issuetype,status,timetracking,worklog,subtasks"
    ];
    return $this->getSprintService()->getSprintIssues($sprintId, $params);
  }

  public function getIssue($issueId)
  {
    $params = ["fields" => "summary,issuetype,status,timetracking,worklog,subtasks"];
    return $this->getIssueService()->get($issueId, $params);
  }
}
