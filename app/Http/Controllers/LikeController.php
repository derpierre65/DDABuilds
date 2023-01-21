<?php

namespace App\Http\Controllers;

use App\Models\DatabaseNotification;
use App\Models\Like\AbstractLike;
use App\Models\User;
use App\Notifications\LikeNotification;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LikeController extends Controller
{
	public function like(Request $request) : array
	{
		$objectID = $request->get('object_id');
		$objectType = $request->get('object_type');
		$likeType = $request->get('like_type');

		if ( !$objectID || !$objectType || !$likeType ) {
			throw new BadRequestHttpException();
		}

		$className = 'App\\Models\\Like\\'.ucfirst($objectType).'Like';
		if ( !class_exists($className) ) {
			throw new BadRequestHttpException();
		}
		elseif ( !is_subclass_of($className, AbstractLike::class) ) {
			throw new BadRequestHttpException(sprintf("Class '%s' does not extend class '%s'.", $className, AbstractLike::class));
		}

		/** @var AbstractLike $like */
		$like = new $className($objectID, auth()->user()->getKey());
		$this->authorize('like', $like->getModel());

		if ( $likeType === 'dislike' && !$like->isEnabledDislikes() ) {
			throw new BadRequestHttpException('Dislike is disabled.');
		}

		$likeModel = $like->getLikeModel();
		$oldLikeValue = $like->getLikeValue();
		$newLikeValue = $likeType === 'like' ? AbstractLike::LIKE : AbstractLike::DISLIKE;
		$oldCounter = $oldLikeValue === AbstractLike::LIKE ? 'likes' : 'dislikes';
		$newCounter = $newLikeValue === AbstractLike::LIKE ? 'likes' : 'dislikes';
		$newState = [];

		if ( $oldLikeValue === null ) {
			if ( $likeModel = $like->createLike($newLikeValue) ) {
				$like->getModel()->increment($newCounter);
				$newState[$newLikeValue] = 1;
			}
		}
		// delete like/dislike
		elseif ( $oldLikeValue === $newLikeValue ) {
			if ( $like->deleteLike() ) {
				$like->getModel()->decrement($newCounter);
				$newState[$newLikeValue] = -1;
			}

			// delete notification
			DatabaseNotification::query()->find($likeModel->notification_id)->delete();
		}
		// delete old like/dislike and add new
		else {
			$likeModel = $like->updateLike($newLikeValue);

			$like->getModel()->decrement($oldCounter);
			$like->getModel()->increment($newCounter);
			$newState[$oldLikeValue] = -1;
			$newState[$newLikeValue] = 1;

			// delete notification
			DatabaseNotification::query()->find($likeModel->notification_id)->delete();
		}

		// send notification
		if ( $oldLikeValue !== $newLikeValue ) {
			/** @var User $user */
			$user = User::query()->find($like->getRecipientID());
			$user->notify(new LikeNotification($like, $likeModel));
		}

		return $newState;
	}
}