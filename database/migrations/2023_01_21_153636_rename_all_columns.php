<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up()
	{
		// we are dropping all foreign keys, LETS GO
		$this->dropForeignKeys();

		Schema::rename('steam_users', 'users');
		Schema::rename('build_watches', 'build_user');
		Schema::rename('build_hero_stats', 'build_hero');
		Schema::rename('build_towers', 'build_wave_tower');

		// renaming many columns, lets go
		$this->renameColumns();
		$this->transformTimestamp();

		// add foreign keys
		$this->addForeignKeys();
	}

	public function down()
	{
	}

	public function dropForeignKeys()
	{
		$this->dropForeign('bug_reports', 'bug_reports_ibfk_1', 'bug_report_ibfk_1');

		$this->dropForeign('bug_report_comments', 'bug_report_comments_ibfk_1', 'bug_report_comment_ibfk_1');
		$this->dropForeign('bug_report_comments', 'bug_report_comments_ibfk_2', 'bugReportID');

		$this->dropForeign('likes', 'likes_ibfk_1', 'like_ibfk_1');
		Schema::table('likes', fn(Blueprint $table) => $table->dropUnique('objectType'));
		Schema::table('likes', fn(Blueprint $table) => $table->dropPrimary(['objectType', 'objectID', 'steamID']));

		$this->dropForeign('map_available_units', 'map_available_units_ibfk_1', false);
		$this->dropForeign('map_available_units', 'map_available_units_ibfk_2', 'map_available_unit_ibfk_2');

		Schema::table('towers', fn(Blueprint $table) => $table->dropIndex('fk_class'));

		$this->dropForeign('builds', 'builds_ibfk_1', 'build_ibfk_1');
		$this->dropForeign('builds', 'builds_ibfk_2', 'map');
		$this->dropForeign('builds', 'builds_ibfk_3', 'difficulty');
		$this->dropForeign('builds', 'builds_ibfk_4', 'gamemodeID');
		Schema::table('builds', fn(Blueprint $table) => $table->dropIndex('fk_buildstatus'));

		$this->dropForeign('build_comments', 'build_comments_ibfk_1', 'build_comment_ibfk_1');
		$this->dropForeign('build_comments', 'build_comments_ibfk_2', 'build_comment_ibfk_2');

		$this->dropForeign('build_hero_stats', 'build_hero_stats_ibfk_1', 'classID');

		$this->dropForeign('build_waves', 'build_waves_ibfk_1', 'fk_build');

		$this->dropForeign('build_towers', 'build_towers_ibfk_1', 'fk_tower');
		$this->dropForeign('build_towers', 'build_towers_ibfk_2', 'build_tower_ibfk_2');

		$this->dropForeign('build_watches', 'build_watches_ibfk_1', false);
		$this->dropForeign('build_watches', 'build_watches_ibfk_2', 'buildID');
	}

	public function renameColumns()
	{
		$renaming = [
			'bug_report_comments' => [
				'commentID' => 'id',
				'bugReportID' => 'bug_report_id',
				'steamID' => 'user_id',
			],
			'bug_reports' => [
				'reportID' => 'id',
				'steamID' => 'user_id',
			],
			'build_comments' => [
				'ID' => 'id',
				'steamID' => 'user_id',
				'buildID' => 'build_id',
			],
			'build_hero' => [
				'buildID' => 'build_id',
				'heroID' => 'hero_id',
			],
			'build_wave_tower' => [
				'towerID' => 'tower_id',
				'buildWaveID' => 'build_wave_id',
				'overrideUnits' => 'size',
			],
			'build_user' => [
				'steamID' => 'user_id',
				'buildID' => 'build_id',
			],
			'build_waves' => [
				'waveID' => 'id',
				'buildID' => 'build_id',
			],
			'builds' => [
				'ID' => 'id',
				'mapID' => 'map_id',
				'difficultyID' => 'difficulty_id',
				'gameModeID' => 'game_mode_id',
				'steamID' => 'user_id',
				'buildStatus' => 'build_status',
				'afkAble' => 'is_afk_able',
				'rifted' => 'is_rifted',
				'hardcore' => 'is_hardcore',
				'timePerRun' => 'time_per_run',
				'expPerRun' => 'exp_per_run',
				'isDeleted' => 'is_deleted',
			],
			'difficulties' => [
				'ID' => 'id',
			],
			'game_modes' => [
				'ID' => 'id',
			],
			'heroes' => [
				'ID' => 'id',
				'isHero' => 'is_hero',
				'isDisabled' => 'is_disabled',
			],
			'likes' => [
				'objectType' => 'object_type',
				'objectID' => 'object_id',
				'steamID' => 'user_id',
				'likeValue' => 'like_value',
				'notificationID' => 'notification_id',
			],
			'map_available_units' => [
				'mapID' => 'map_id',
				'difficultyID' => 'difficulty_id',
			],
			'map_categories' => [
				'ID' => 'id',
			],
			'maps' => [
				'ID' => 'id',
				'mapCategoryID' => 'map_category_id',
			],
			'users' => [
				'ID' => 'id',
				'avatarHash' => 'avatar_hash',
			],
			'towers' => [
				'ID' => 'id',
				'unitType' => 'unit_type',
				'unitCost' => 'unit_cost',
				'maxUnitCost' => 'max_unit_cost',
				'manaCost' => 'mana',
				'heroClassID' => 'hero_id',
				'isResizable' => 'is_resizable',
				'isRotatable' => 'is_rotatable',
			],
		];

		foreach ( $renaming as $tableName => $columns ) {
			Schema::table($tableName, function (Blueprint $table) use ($columns) {
				foreach ( $columns as $oldName => $newName ) {
					$table->renameColumn($oldName, $newName);
				}
			});
		}
	}

	public function transformTimestamp()
	{
		$transform = array_unique([
			'bug_reports.time',
			'bug_report_comments.time',
			'build_comments.date',
			'builds.date',
			'likes.date',
		]);

		foreach ( $transform as $tableColumn ) {
			[$table, $column] = explode('.', $tableColumn);
			if ( !Schema::hasColumn($table, 'created_at') ) {
				Schema::table($table, fn(Blueprint $table) => $table->timestamps());
			}

			DB::update('UPDATE '.$table.' SET created_at = FROM_UNIXTIME('.$column.'), updated_at = FROM_UNIXTIME('.$column.');');

			Schema::dropColumns($table, [$column]);
		}

		Schema::table('users', fn(Blueprint $table) => $table->timestamps());
	}

	public function addForeignKeys() {
		$foreignKeys = [
			'bug_report_comments' => [
				'bug_report_id' => 'bug_reports.id',
				'user_id' => 'users.id',
			],
			'bug_reports' => [
				'user_id' => 'users.id',
			],
			'build_comments' => [
				'build_id' => 'builds.id',
				'user_id' => 'users.id',
			],
			'build_hero' => [
				'build_id' => 'builds.id',
				'hero_id' => 'heroes.id',
			],
			'build_wave_tower' => [
				'tower_id' => 'towers.id',
				'build_wave_id' => 'build_waves.id',
			],
			'build_user' => [
				'build_id' => 'builds.id',
				'user_id' => 'users.id',
			],
			'build_waves' => [
				'build_id' => 'builds.id',
			],
			'builds' => [
				'map_id' => 'maps.id',
				'difficulty_id' => 'difficulties.id',
				'user_id' => 'users.id',
				'game_mode_id' => 'game_modes.id',
			],
			'likes' => [
				'user_id' => 'users.id',
				'notification_id' => 'notifications.id',
			],
			'map_available_units' => [
				'map_id' => 'maps.id',
				'difficulty_id' => 'difficulties.id',
			],
			'maps' => [
				'map_category_id' => 'map_categories.id',
			],
			'towers' => [
				'hero_id' => 'heroes.id',
			],
		];

		foreach ( $foreignKeys as $tableName => $keys ) {
			Schema::table($tableName, function(Blueprint $table) use($keys) {
				foreach ( $keys as $column => $reference ) {
					[$refTable, $feColumn] = explode('.', $reference);
					$table->foreign($column)
						->references($feColumn)
						->on($refTable)
						->cascadeOnDelete()
						->cascadeOnUpdate();
				}
			});
		}
	}

	private function dropForeign(string $tableName, string $foreignName, string|bool|null $indexName = null)
	{
		Schema::table($tableName, fn(Blueprint $table) => $table->dropForeign($foreignName));
		if ( $indexName !== false ) {
			Schema::table($tableName, fn(Blueprint $table) => $table->dropIndex($indexName ?? $foreignName));
		}
	}
};
