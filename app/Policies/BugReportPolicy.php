<?php

namespace App\Policies;

use App\Models\BugReport;
use App\Models\SteamUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class BugReportPolicy {
	use HandlesAuthorization;

	/**
	 * maintainer can list all and manage the bug reports (close, create new github issue or delete)
	 */
	public const MAINTAINER = [
		'76561198054589426', // derpierre65
	];

	public function isMaintainer(SteamUser $steamUser) {
		return $steamUser->isMaintainer();
	}

	public function viewAny(SteamUser $steamUser) {
		return request()->query->getBoolean('mine', false) && $steamUser->ID || $this->isMaintainer($steamUser);
	}

	public function view(SteamUser $steamUser, BugReport $bugReport) {
		return $bugReport->steamID === $steamUser->ID || $this->isMaintainer($steamUser);
	}

	public function create(SteamUser $steamUser) {
		return $steamUser->ID;
	}

	public function update(SteamUser $steamUser, BugReport $bugReport) {
		return $this->isMaintainer($steamUser);
	}

	public function delete(SteamUser $steamUser, BugReport $bugReport) {
		return $this->isMaintainer($steamUser);
	}
}