<?php

namespace App\Http\Controllers\v1;

use App\Constants\DefaultValue;
use App\Http\Controllers\Controller;
use App\Http\RestResponse;
use App\Service\JiraService;
use Illuminate\Http\Request;

class TestController extends Controller
{
  private $jira;

  public function __construct()
  {
    $this->jira = new JiraService();
  }

  public function index(Request $request)
  {
    return RestResponse::data($this->jira->getSprints());
  }
}
