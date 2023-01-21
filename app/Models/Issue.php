<?php

namespace App\Models;

use App\Models\Traits\HasSteamUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int   $reportID
 * @property-read int   $time
 * @property-read string    $title
 * @property-read string    $steamID
 * @property-read string    $description
 * @property-read int   $status
 * @property-read SteamUser $user
 */
class Issue extends Model {
	use HasSteamUser, HasFactory;

	public const STATUS_OPEN = 1;

	public const STATUS_CLOSED = 2;

	public const WAIT_TIME = 60;

	protected $table = 'bug_report';

	protected $perPage = 20;

	public $timestamps = false;

	protected $primaryKey = 'reportID';

	protected $fillable = [
		'steamID',
		'time',
		'title',
		'description',
		'status',
	];

	protected $hidden = [
		'steamID',
	];

	public function getComments() {
		return IssueComment::where('bugReportID', $this->reportID);
	}

	public function comments() {
		return $this->hasMany(IssueComment::class, 'bugReportID', 'reportID');
	}
}