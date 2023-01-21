<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int $unit_type (0 = du, 1 = mu)
 * @property int $unit_cost
 * @property int $max_unit_cost
 * @property int $mana
 * @property int $hero_id
 * @property string $name
 * @property string $image_size
 * @property int $is_resizable
 * @property int $is_rotatable
 *
 * @property-read string $public_path
 * @property-read Hero $hero
 */
class Tower extends Model
{
	public $timestamps = false;

	protected $guarded = [];

	protected $casts = [
		'is_resizable' => 'bool',
		'is_rotatable' => 'bool',
	];

	public function hero() : BelongsTo
	{
		return $this->belongsTo(Hero::class);
	}

	public function getPublicPathAttribute() : string
	{
		$name = $this->name;
		if ( $this->is_resizable && $this->pivot) {
			$name .= '_'.(($this->pivot->size ? : $this->unit_cost) - $this->unit_cost);
		}

		return public_path('assets/images/tower/'.$name.'.png');
	}
}