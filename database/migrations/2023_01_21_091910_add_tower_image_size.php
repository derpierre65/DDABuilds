<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTowerImageSize extends Migration
{
	public function up()
	{
		Schema::table('tower', function (Blueprint $table) {
			$table->string('image_size', 8)->default('')->after('name');
		});
	}

	public function down()
	{
		Schema::dropColumns('tower', ['image_size']);
	}
}
