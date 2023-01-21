<?php

namespace App\Models;

use App\Models\Build\BuildComment;
use App\Models\Build\BuildWave;
use App\Models\Like\ILikeableModel;
use App\Models\Traits\HasUserRelation;
use App\Models\Traits\TLikeable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read int $id
 * @property string $user_id
 * @property string $author
 * @property string $title
 * @property string $exp_per_run
 * @property string $time_per_run
 * @property string $description
 * @property int $views
 * @property int $game_mode_id
 * @property int $difficulty_id
 * @property int $map_id
 * @property int $comments
 * @property int $is_deleted
 * @property int $build_status
 * @property int $is_afk_able
 * @property int $is_hardcore
 * @property int $is_rifted
 * @property int $likes
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 *
 * @property-read GameMode $gameMode
 * @property-read Difficulty $difficulty
 * @property-read Map $map
 * @property-read User $watchStatus
 * @property-read Collection<Hero> $heroStats
 * @property-read Collection<BuildWave> $waves
 */
class Build extends Model implements ILikeableModel
{
	use HasFactory;
	use HasUserRelation;
	use TLikeable;

	/** @var int public build status (everyone can view the build) */
	public const STATUS_PUBLIC = 1;

	/** @var int unlisted build status (build ist not listed, but everyone can view the build) */
	public const STATUS_UNLISTED = 2;

	/** @var int private build status (only creator can view the build) */
	public const STATUS_PRIVATE = 3;

	protected $perPage = 21;

	protected $fillable = [
		'map_id',
		'difficulty_id',
		'user_id',
		'author',
		'title',
		'description',
		'build_status',
		'game_mode_id',
		'is_hardcore',
		'is_afk_able',
		'is_rifted',
		'views',
		'likes',
		'comments',
		'time_per_run',
		'exp_per_run',
		'is_deleted',
	];

	public array $validSortFields = [
		'author',
		'likes',
		'map_id',
		'title',
		'views',
		'created_at',
		'difficulty_id',
		'game_mode_id',
	];

	protected $casts = [
		'is_hardcore' => 'bool',
		'is_afk_able' => 'bool',
		'is_rifted' => 'bool',
	];

	public function watchStatus() : BelongsToMany
	{
		return $this->belongsToMany(User::class)->where('user_id', auth()->user()?->getKey() ?? 0);
	}

	public function map() : BelongsTo
	{
		return $this->belongsTo(Map::class);
	}

	public function difficulty() : BelongsTo
	{
		return $this->belongsTo(Difficulty::class);
	}

	public function gameMode() : BelongsTo
	{
		return $this->belongsTo(GameMode::class);
	}

	public function waves() : HasMany
	{
		return $this->hasMany(BuildWave::class);
	}

	public function heroStats() : BelongsToMany
	{
		return $this->belongsToMany(Hero::class)
			->withPivot([
				'hp',
				'damage',
				'range',
				'rate',
			]);
	}

	public function commentList() : HasMany
	{
		return $this->hasMany(BuildComment::class);
	}

	public function scopeSort(Builder $query, $column = 'created_at', $direction = 'asc')
	{
		if ( $column === null && $direction === null ) {
			$column = 'created_at';
			$direction = 'desc';
		}

		if ( $column === null ) {
			$column = 'created_at';
		}
		if ( $direction === null ) {
			$direction = 'asc';
		}

		if ( in_array($column, $this->validSortFields) ) {
			$query->orderBy($this->table.'.'.$column, $direction);
		}
	}

	public function generateThumbnail() : bool
	{
		$mapResource = imagecreatefrompng($this->map->getPublicPathAttribute());
		if ( !$mapResource ) {
			Log::debug('map resource failed');
			return false;
		}

		$mapResource = imagescale($mapResource, 1024, 1024, IMG_BICUBIC_FIXED);
		if ( !$mapResource ) {
			Log::debug('scale failed', [get_loaded_extensions()]);
			return false;
		}

		/** @var Tower $buildTower */
		foreach ( $this->waves()->first()->towers()->with(['hero'])->get() as $index => $buildTower ) {
			$towerSizeX = $towerSizeY = 35;

			if ( $buildTower->hero->name === 'monk' ) {
				$towerSizeX = $towerSizeY = 100;
			}
			elseif ( $buildTower->hero->name === 'huntress' ) {
				$towerSizeX = $towerSizeY = 45;
			}
			elseif ( $buildTower->hero->name === 'seriesEVA' ) {
				$towerSizeX = $towerSizeY = 0;
			}

			$towerResource = imagecreatefrompng($buildTower->getPublicPathAttribute());
			if ( $towerSizeX && $towerSizeY ) {
				$towerResource = imagescale($towerResource, $towerSizeX, $towerSizeY, IMG_BICUBIC_FIXED);
			}
			else {
				$imageInfo = getimagesize($buildTower->getPublicPathAttribute());
				$towerSizeX = $imageInfo[0];
				$towerSizeY = $imageInfo[1];
			}

			$rotation = -$buildTower->pivot->rotation;
			$x = $buildTower->pivot->x;
			$y = $buildTower->pivot->y;
			if ( $rotation ) {
				$png = imagecreatetruecolor($towerSizeX, $towerSizeY);
				imagesavealpha($png, true);
				$pngTransparency = imagecolorallocatealpha($png, 0, 0, 0, 127);
				$towerResource = imagerotate($towerResource, $rotation, $pngTransparency);
				$newTowerSizeX = imagesx($towerResource);
				$newTowerSizeY = imagesy($towerResource);
				$x -= ($newTowerSizeX - $towerSizeX) / 2;
				$y -= ($newTowerSizeY - $towerSizeY) / 2;
				$towerSizeX = $newTowerSizeX;
				$towerSizeY = $newTowerSizeY;
			}

			imagecopy($mapResource, $towerResource, $x, $y, 0, 0, $towerSizeX, $towerSizeY);
		}

		return imagepng(
			imagescale($mapResource, 200, 200, IMG_BICUBIC_FIXED),
			$this->getPublicThumbnailPathAttribute()
		);
	}

	public function getPublicThumbnailPathAttribute() : string
	{
		return Storage::disk('public')->path('thumbnails/'.$this->getKey().'.png');
	}

	public function getNotificationData() : array
	{
		return [
			'id' => $this->getKey(),
			'title' => $this->title,
		];
	}

	public function syncStats(array $heroStats)
	{
		$values = [];
		foreach ( $heroStats as $key => $attributes ) {
			if ( $attributes['hp'] || $attributes['damage'] || $attributes['rate'] || $attributes['range'] ) {
				$values[$attributes['id']] = Arr::except($attributes, ['id']);
			}
		}

		$this->heroStats()->sync($values);
	}
}