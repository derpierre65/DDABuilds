<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('session');
        Schema::dropIfExists('locale');
		Schema::rename('bug_report', 'bug_reports');
		Schema::rename('bug_report_comment', 'bug_report_comments');
		Schema::rename('build', 'builds');
		Schema::rename('build_comment', 'build_comments');
		Schema::rename('build_tower', 'build_towers');
		Schema::rename('build_watch', 'build_watches');
		Schema::rename('build_wave', 'build_waves');
		Schema::rename('build_stats', 'build_hero_stats');
		Schema::rename('difficulty', 'difficulties');
		Schema::rename('game_mode', 'game_modes');
		Schema::rename('hero', 'heroes');
		Schema::rename('like', 'likes');
		Schema::rename('map', 'maps');
		Schema::rename('map_available_unit', 'map_available_units');
		Schema::rename('map_category', 'map_categories');
		Schema::rename('steam_user', 'steam_users');
		Schema::rename('tower', 'towers');
    }

    public function down()
    {
        //
    }
};