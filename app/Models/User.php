<?php

namespace App\Models;

use App\Models\Traits\Notifiable;
use App\Policies\BugReportPolicy;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

/**
 * @property-read string $id
 * @property string $name
 * @property string $avatar_hash
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
	use Authenticatable, Authorizable, HasFactory, Notifiable;

	public const AVATAR_SMALL = 1;

	public const AVATAR_MEDIUM = 2;

	public const AVATAR_BIG = 3;

	protected $guarded = [];

	protected $keyType = 'string';

	public $incrementing = false;

	/**
	 * get the avatar from steam user in given size
	 *
	 * @param int $size
	 *
	 * @return string
	 */
	public function getAvatarUrlAttribute($size = self::AVATAR_MEDIUM) : string
	{
		$hashAdditional = '';
		if ( $size === self::AVATAR_BIG ) {
			$hashAdditional = '_full';
		}
		elseif ( $size === self::AVATAR_MEDIUM ) {
			$hashAdditional = '_medium';
		}

		return 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/'.substr($this->avatar_hash, 0, 2).'/'.$this->avatar_hash.$hashAdditional.'.jpg';
	}

	public function getIsMaintainerAttribute() : bool
	{
		return in_array($this->id, BugReportPolicy::MAINTAINER);
	}

	public function getNotificationData() : array
	{
		return [
			'id' => $this->getKey(),
			'name' => $this->name,
		];
	}

	public function setRememberToken($value)
	{
		// do nothing - ignore remember token
	}
}