<?php

namespace App\Models;

use App\Models\Traits\HasSteamUser;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $objectType
 * @property-read int $objectID
 * @property-read string $steamID
 * @property-read int $likeValue
 * @property-read string $date
 * @property-read string $notificationID
 */
class Like extends Model {
	use HasSteamUser;

	protected $primaryKey = null;

	public $incrementing = false;

	public $timestamps = false;

	protected $fillable = [
		'objectType',
		'objectID',
		'steamID',
		'likeValue',
		'date',
		'notificationID',
	];
}