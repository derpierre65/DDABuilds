<?php

namespace App\Models\Traits;

use App\Models\SteamUser;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 *
 * @property-read SteamUser $user
 */
trait HasSteamUser {
	public function user() {
		return $this->hasOne(SteamUser::class, 'ID', 'steamID');
	}
}