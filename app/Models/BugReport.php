<?php

namespace App\Models;

use App\Models\Traits\HasUserRelation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property int $time
 * @property string $title
 * @property string $user_id
 * @property string $description
 * @property int $status
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 *
 * @property-read Collection<BugReportComment> $comments
 */
class BugReport extends Model
{
	use HasUserRelation, HasFactory;

	public const STATUS_OPEN = 1;

	public const STATUS_CLOSED = 2;

	public const WAIT_TIME = 60;

	protected $guarded = [];

	public function comments() : HasMany
	{
		return $this->hasMany(BugReportComment::class);
	}
}