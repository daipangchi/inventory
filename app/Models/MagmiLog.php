<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MagmiLog
 *
 * @property integer $id
 * @property string $data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MagmiLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MagmiLog whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MagmiLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MagmiLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MagmiLog extends Model
{
    protected $table = 'magmi_logs';

    protected $fillable = [
        'data',
    ];
}
