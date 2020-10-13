<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('street_address')->nullable();
			$table->string('city')->nullable();
            $table->string('state_province', 2)->nullable();
            $table->string('zip_code')->nullable();
            $table->string('email');
            $table->string('phone_home');
            $table->string('phone_work')->nullable();
            $table->string('child_name')->nullable();
            $table->tinyInteger('grade')->nullable();
            $table->timestamps();

            //add indexes
            $table->unique('email');
			$table->index('created_at');
			$table->index('zip_code');

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
        Schema::dropIfExists('customers');
    }
}
