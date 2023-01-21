<?php

namespace App\Models;

use App\Models\Traits\HasUserRelation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $bug_report_id
 * @property int $time
 * @property string $user_id
 * @property string $description
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class BugReportComment extends Model
{
	use HasUserRelation, HasFactory;

	public const WAIT_TIME = 60;

	protected $guarded = [];
}