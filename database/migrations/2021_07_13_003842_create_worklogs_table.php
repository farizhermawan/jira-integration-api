<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorklogsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('worklogs', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->uuid('issue_id');
      $table->unsignedBigInteger('sprint_id');
      $table->unsignedBigInteger('user_id');
      $table->string('parent', 15);
      $table->string('issue_key', 15);
      $table->date('date');
      $table->integer('time_spent');
      $table->timestamps();

      $table->index(['sprint_id', 'parent']);
      $table->index(['sprint_id', 'issue_key']);
      $table->index(['sprint_id', 'user_id']);

      $table->foreign('issue_id')->references('id')->on('issues')
        ->onDelete('cascade')
        ->onUpdate('cascade');

      $table->foreign('sprint_id')->references('id')->on('sprints')
        ->onDelete('cascade')
        ->onUpdate('cascade');

      $table->foreign('user_id')->references('id')->on('users')
        ->onDelete('cascade')
        ->onUpdate('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('worklogs');
  }
}
