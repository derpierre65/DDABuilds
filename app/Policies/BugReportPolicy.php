<?php

namespace App\Policies;

use App\Models\BugReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BugReportPolicy
{
	use HandlesAuthorization;

	/**
	 * maintainer can list all and manage the bug reports (close, create new github issue or delete)
	 */
	public const MAINTAINER = [
		'76561198054589426', // derpierre65
	];

	public function isMaintainer(User $user) : bool
	{
		return $user->getIsMaintainerAttribute();
	}

	public function viewAny(User $user) : bool
	{
		return request()->query->getBoolean('mine', false) || $this->isMaintainer($user);
	}

	public function view(User $user, BugReport $bugReport) : bool
	{
		return $bugReport->user_id === $user->id || $this->isMaintainer($user);
	}

	public function create(User $user) : bool
	{
		return true;
	}

	public function update(User $user, BugReport $bugReport) : bool
	{
		return $this->isMaintainer($user);
	}

	public function delete(User $user, BugReport $bugReport) : bool
	{
		return $this->isMaintainer($user);
	}
}