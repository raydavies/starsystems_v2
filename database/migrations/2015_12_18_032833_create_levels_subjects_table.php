<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLevelsSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('levels_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('level_id', null, true);
			$table->integer('subject_id', null, true);
            $table->timestamps();

			$table->index(['level_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('levels_subjects');
    }
}
