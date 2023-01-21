<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;
use Illuminate\Support\Carbon;

/**
 * @property-read string $id
 * @property-read string $type
 * @property-read string $notifiable_type
 * @property-read int $notifiable_id
 * @property-read string $data
 * @property-read Carbon|null $read_at
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class DatabaseNotification extends BaseDatabaseNotification
{
}