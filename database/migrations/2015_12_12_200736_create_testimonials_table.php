<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestimonialsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('testimonials', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name')->nullable();
			$table->string('city');
			$table->string('state_province', 2);
			$table->text('comment');
			$table->tinyInteger('flag_active', null, true)->default(0);
			$table->integer('sort')->default(0);
			$table->timestamps();

			//add indexes
			$table->index('created_at');
			$table->index(['state_province', 'city', 'flag_active']);

			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('testimonials');
	}

}
