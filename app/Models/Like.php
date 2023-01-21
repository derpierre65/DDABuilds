<?php

namespace App\Models;

use App\Models\Traits\HasUserRelation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $object_type
 * @property int $object_id
 * @property string $user_id
 * @property int $like_value
 * @property string $notification_id
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class Like extends Model
{
	use HasUserRelation;

	protected $primaryKey = null;

	public $incrementing = false;

	public $timestamps = false;

	protected $guarded = [];
}