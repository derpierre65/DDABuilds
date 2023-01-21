<?php

namespace App\Models;

use App\Models\Traits\HasSteamUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $reportID
 * @property-read int $time
 * @property-read string $title
 * @property-read string $steamID
 * @property-read string $description
 * @property-read int $status
 * @property-read SteamUser $user
 */
class BugReport extends Model
{
	use HasSteamUser, HasFactory;

	public const STATUS_OPEN = 1;

	public const STATUS_CLOSED = 2;

	public const WAIT_TIME = 60;

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

	public function comments() : HasMany
	{
		return $this->hasMany(BugReportComment::class, 'bugReportID', 'reportID');
	}
}