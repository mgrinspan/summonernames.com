<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateBaseTables extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('history', function (Blueprint $table) {
			$table->increments('id');

			$table->string('name');
			$table->ipAddress('ip');
			$table->string('server');
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});

		Schema::create('feedback', function (Blueprint $table) {
			$table->increments('id');

			$table->string('email')->nullable();
			$table->text('message');
			$table->ipAddress('ip');
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('history');
		Schema::dropIfExists('feedback');
	}
}
