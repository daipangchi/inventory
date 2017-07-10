<?php

namespace App\Models;

use App\Models\Merchants\Merchant;
use App\Models\Products\ChangeLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SyncJobLog
 *
 * @property integer $id
 * @property integer $merchant_id
 * @property string $channel
 * @property integer $products_created
 * @property integer $products_removed
 * @property integer $products_updated
 * @property string $data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $ended_at
 * @property-read mixed $duration
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\ChangeLog[] $changeLogs
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SyncJobLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SyncJobLog whereMerchantId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SyncJobLog whereChannel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SyncJobLog whereProductsCreated($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SyncJobLog whereProductsRemoved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SyncJobLog whereProductsUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SyncJobLog whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SyncJobLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SyncJobLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SyncJobLog whereEndedAt($value)
 * @mixin \Eloquent
 */
class SyncJobLog extends Model
{
    /**
     * @var string
     */
    protected $table = 'sync_job_logs';

    /**
     * @var array
     */
    protected $fillable = [
        'merchant_id',
        'channel',
        'products_added',
        'products_removed',
        'products_updated',
        'data',
        'failed',
        'ended_at',
    ];

    /**
     * @var array
     */
    protected $appends = ['duration'];

    /**
     * @param $data
     */
    public function setDataAttribute($data)
    {
        if (is_array($data))  {
            $this->attributes['data'] = \GuzzleHttp\json_encode($data);
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    public function getDataAttribute($data)
    {
        if (is_string($data) && $data) {
            return \GuzzleHttp\json_decode($data, true);
        }

        return $data;
    }

    /**
     * @param $date
     * @return Carbon|null
     */
    public function getEndedAtAttribute($date)
    {
        if($date == null) return '';
        
        return Carbon::createFromFormat('Y-m-d H:i:s', $date);
    }

    /**
     * @return string
     */
    public function getDurationAttribute()
    {
        return seconds_to_time($this->created_at->diffInSeconds($this->ended_at));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function changeLogs()
    {
        return $this->hasMany(ChangeLog::class, 'sync_job_log_id');
    }

    /**
     * @return $this
     */
    public function incrementProductsUpdated()
    {
        $this->products_updated++;
        $this->save();

        return $this;
    }
    
    protected static function boot() {
        parent::boot();

        static::deleting(function($log) { // before delete() method call this
             $log->changeLogs()->delete();
        });
    }
}
