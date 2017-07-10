<?php

namespace App\Models;

use App\Models\Merchants\Merchant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Schedule
 *
 * @property integer $id
 * @property integer $merchant_id
 * @property string $channel
 * @property string $run_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Merchants\Merchant $merchant
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Schedule whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Schedule whereMerchantId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Schedule whereChannel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Schedule whereRunAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Schedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Schedule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Schedule extends Model
{
    public $fillable = [
        'merchant_id',
        'channel',
        'run_at',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function getRunAtAttribute($time)
    {
        list($h, $m, $s) = explode(':', $time);

        return Carbon::createFromTime($h, $m, $s);
    }
}
