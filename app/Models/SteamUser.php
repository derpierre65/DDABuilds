<?php

namespace App\Models;

use App\Models\Traits\Notifiable;
use App\Policies\IssuePolicy;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

/**
 * @property-read string ID
 * @property-read string name
 * @property-read string avatarHash
 */
class SteamUser extends Model implements AuthenticatableContract, AuthorizableContract {
	use Authenticatable, Authorizable, HasFactory, Notifiable;

	public const AVATAR_SMALL = 1;

	public const AVATAR_MEDIUM = 2;

	public const AVATAR_BIG = 3;

	protected $table = 'steam_user';

	protected $primaryKey = 'ID';

	protected $casts = [
		'ID' => 'string',
	];

	protected $fillable = [
		'ID',
		'name',
		'avatarHash',
	];

	public $timestamps = false;

	public $incrementing = false;

	public function setRememberToken($value) {
		// do nothing - ignore remember token
	}

	/**
	 * get the avatar from steam user in given size
	 *
	 * @param int $size
	 *
	 * @return string
	 */
	public function getAvatar($size = self::AVATAR_MEDIUM) : string {
		$hashAdditional = '';
		if ( $size === self::AVATAR_BIG ) {
			$hashAdditional = '_full';
		}
		elseif ( $size === self::AVATAR_MEDIUM ) {
			$hashAdditional = '_medium';
		}

		return 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/'.substr($this->avatarHash, 0, 2).'/'.$this->avatarHash.$hashAdditional.'.jpg';
	}

	public function isMaintainer() : bool {
		return in_array($this->ID, IssuePolicy::MAINTAINER);
	}

	public function authInfo() : array {
		return array_merge($this->toArray(), [
			'unreadNotifications' => $this->unreadNotifications->count(),
			'isMaintainer' => $this->isMaintainer(),
		]);
	}

	public function getNotificationData() : array {
		return [
			'ID' => $this->ID,
			'name' => $this->name,
		];
	}
}