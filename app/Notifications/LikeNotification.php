<?php

namespace App\Notifications;

use App\Models\Like;
use App\Models\Like\AbstractLike;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LikeNotification extends Notification
{
	use Queueable;

	public function __construct(protected AbstractLike $like, protected Like $likeObject)
	{
	}

	public function getLikeObject() : Like
	{
		return $this->likeObject;
	}

	public function via($notifiable) : array
	{
		return ['database'];
	}

	public function toDatabase($notifiable) : array
	{
		return array_merge($this->like->getNotificationData(), [
			'like_value' => $this->getLikeObject()->like_value,
			'context' => $this->like->objectType,
			'user' => User::query()->find($this->getLikeObject()->user_id)->first()->getNotificationData(),
		]);
	}
}
