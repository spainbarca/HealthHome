<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetupCountriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Creates the users table
		Schema::create('countries', function (Blueprint $table)
		{
		    $table->id();
			$table->string('name', 255)->nullable();
			$table->string('currency', 255)->nullable();
			$table->string('currency_symbol', 20)->nullable();
			$table->string('iso_3166_2', 2)->nullable();
			$table->string('iso_3166_3', 3)->nullable();
            $table->string('iso_numeric', 3)->nullable()->unique();
			$table->string('calling_code', 20)->nullable();
			$table->string('flag')->nullable();
            $table->string('currency_code', 255)->nullable();
		});

		Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade')->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('countries');
        Schema::dropIfExists('states');
	}

}
