<?php

namespace App\Models\Build;

use App\Models\Build;
use App\Models\Like\ILikeableModel;
use App\Models\Traits\HasBuildRelation;
use App\Models\Traits\HasUserRelation;
use App\Models\Traits\TLikeable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read int $id
 * @property string $user_id
 * @property string $description
 * @property int $build_id
 * @property int $likes
 * @property int $dislikes
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class BuildComment extends Model implements ILikeableModel
{
	use HasUserRelation;
	use HasBuildRelation;
	use TLikeable;

	public $timestamps = false;

	protected $fillable = [];

	public function getLikeObjectType() : string
	{
		return 'comment';
	}
}