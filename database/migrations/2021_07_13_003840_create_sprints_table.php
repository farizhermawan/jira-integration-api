<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSprintsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('sprints', function (Blueprint $table) {
      $table->unsignedBigInteger('id')->primary();
      $table->unsignedBigInteger("board_id");
      $table->string('name', 50);
      $table->string("state", 10);
      $table->date("start_date");
      $table->date("end_date");
      $table->timestamps();

      $table->index(['board_id', 'state']);

      $table->foreign('board_id')->references('id')->on('boards')
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
    Schema::dropIfExists('sprints');
  }
}
