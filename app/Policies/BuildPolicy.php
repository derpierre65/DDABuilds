<?php

namespace App\Policies;

use App\Models\Build;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BuildPolicy {
	use HandlesAuthorization;

	public function viewAny(?User $steamUser) {
		return true;
	}

	public function view(?User $steamUser, Build $build) {
		if ( $build->is_deleted ) {
			throw new NotFoundHttpException();
		}

		if ( $build->build_status !== Build::STATUS_PRIVATE ) {
			return true;
		}

		if ( $steamUser === null || $steamUser->id !== $build->user_id ) {
			return false;
		}

		return true;
	}

	public function create(User $steamUser) {
		return $steamUser->id;
	}

	public function update(User $steamUser, Build $build) {
		if ( $build->is_deleted ) {
			throw new NotFoundHttpException();
		}

		return $steamUser->id === $build->user_id;
	}

	public function delete(User $steamUser, Build $build) {
		return $this->update($steamUser, $build);
	}

	public function like(User $steamUser, Build $build) {
		if ( !$this->view($steamUser, $build) ) {
			return false;
		}

		return $steamUser->id !== $build->user_id;
	}

	public function watch(User $steamUser, Build $build) {
		return $this->like($steamUser, $build);
	}
}