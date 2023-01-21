<?php

namespace App\Models\Like;

use App\Models\Like;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

abstract class AbstractLike
{
	public const LIKE = 1;

	public const DISLIKE = -1;

	protected static string $baseClass;

	protected static bool $enabledDislikes = false;

	public string $objectType;

	protected ?Model $object = null;

	/** @var Like */
	protected $likeObject;

	public function __construct(protected int $objectId)
	{
		if ( empty(static::$baseClass) ) {
			throw new BadRequestException('Base class not specified');
		}

		if ( !is_subclass_of(static::$baseClass, ILikeableModel::class) ) {
			throw new BadRequestException(sprintf('Class \'%s\' does not extend class \'%s\'.', static::$baseClass, ILikeableModel::class));
		}
		elseif ( !$this->getModel() ) {
			throw new BadRequestException('objectID not found');
		}

		$classParts = explode('\\', get_class($this));
		$this->objectType = lcfirst(substr(array_pop($classParts), 0, -4));
	}

	abstract public function getNotificationData() : array;

	public function getRecipientID()
	{
		return $this->getModel() ? $this->getModel()->user_id : null;
	}

	public function getLikeValue() : ?int
	{
		return $this->getLikeModel()?->like_value;
	}

	public function getObjectId() : int
	{
		return $this->objectId;
	}

	public function isEnabledDislikes() : bool
	{
		return static::$enabledDislikes;
	}

	public function getObjectType() : string
	{
		return $this->objectType;
	}

	/**
	 * @return Model|null
	 */
	public function getModel() : ?Model
	{
		if ( $this->object === null ) {
			/** @var Model $baseClass */
			$baseClass = static::$baseClass;
			$this->object = $baseClass::query()->find($this->objectId);
		}

		return $this->object;
	}

	public function getLikeModel() : ?Like
	{
		if ( $this->likeObject === null ) {
			$this->likeObject = Like::query()
				->where($this->getLikeObjectSearchCondition())
				->first();
		}

		return $this->likeObject;
	}

	public function createLike(int $newLikeValue) : ?Like
	{
		return Like::query()
			->create(array_merge($this->getLikeObjectSearchCondition(), [
				'likeValue' => $newLikeValue,
			]));
	}

	public function updateLike(int $newLikeValue) : ?Like
	{
		Like::query()
			->where($this->getLikeObjectSearchCondition())
			->update([
				'likeValue' => $newLikeValue,
			]);

		return $this->likeObject/*->refresh()*/ ;
	}

	public function deleteLike() : bool
	{
		return Like::query()
			->where($this->getLikeObjectSearchCondition())
			->delete();
	}

	protected function getLikeObjectSearchCondition() : array
	{
		return [
			'object_type' => $this->objectType,
			'object_id' => $this->objectId,
			'user_id' => auth()->user()?->getKey(),
		];
	}
}