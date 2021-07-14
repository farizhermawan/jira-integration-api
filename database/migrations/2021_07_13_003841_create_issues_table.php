<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIssuesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('issues', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->unsignedBigInteger('ref');
      $table->unsignedBigInteger('sprint_id');
      $table->string('type', 10);
      $table->string('parent', 15);
      $table->string('issue_key', 15);
      $table->text('summary');
      $table->integer('original_estimate')->nullable();
      $table->integer('remaining_estimate')->nullable();
      $table->integer('time_spent')->nullable();
      $table->string("state", 20);
      $table->timestamps();

      $table->unique(['sprint_id', 'issue_key']);
      $table->index(['sprint_id', 'type']);
      $table->index(['sprint_id', 'parent']);
      $table->index(['sprint_id', 'state']);

      $table->foreign('sprint_id')->references('id')->on('sprints')
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
    Schema::dropIfExists('issues');
  }
}
