<?php

namespace App\Policies;

use App\Models\Issue;
use App\Models\SteamUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class IssuePolicy {
	use HandlesAuthorization;

	/**
	 * maintainer list to see all issues and can manage the issues (close)
	 */
	public const MAINTAINER = [
		'76561198054589426', // derpierre65
		'76561198011599149', // kazeshoni
	];

	public function isMaintainer(SteamUser $steamUser) {
		return $steamUser->isMaintainer();
	}

	public function viewAny(SteamUser $steamUser) {
		return request()->query->getBoolean('mine', false) && $steamUser->ID || $this->isMaintainer($steamUser);
	}

	public function view(SteamUser $steamUser, Issue $issue) {
		return $issue->steamID === $steamUser->ID || $this->isMaintainer($steamUser);
	}

	public function create(SteamUser $steamUser) {
		return $steamUser->ID;
	}

	public function update(SteamUser $steamUser, Issue $issue) {
		return $this->isMaintainer($steamUser);
	}

	public function delete(SteamUser $steamUser, Issue $issue) {
		return $this->isMaintainer($steamUser);
	}
}
