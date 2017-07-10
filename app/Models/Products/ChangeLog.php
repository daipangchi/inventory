<?php

namespace App\Models\Products;

use App\Models\Products\ChangeLogDataTypes\CreatedDataType;
use App\Models\Products\ChangeLogDataTypes\RemovedDataType;
use App\Models\Products\ChangeLogDataTypes\UpdatedDataType;
use App\Models\SyncJobLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * App\Models\Products\ChangeLog
 *
 * @property integer $id
 * @property integer $sync_job_log_id
 * @property integer $product_id
 * @property string $channel
 * @property string $action
 * @property string $data
 * @property boolean $exported_to_customer_portal
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Products\Product $product
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\ChangeLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\ChangeLog whereSyncJobLogId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\ChangeLog whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\ChangeLog whereChannel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\ChangeLog whereAction($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\ChangeLog whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\ChangeLog whereExportedToCustomerPortal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\ChangeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\ChangeLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ChangeLog extends Model
{
    protected $table = 'products_change_logs';

    protected $fillable = [
        'sync_job_log_id',
        'product_id',
        'channel',
        'action',
        'data',
        'exported_to_customer_portal',
    ];

    const ACTION_CREATED = 'created';
    const ACTION_REMOVED = 'removed';
    const ACTION_UPDATED = 'updated';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @param $data
     * @return string
     */
    public function setDataAttribute($data)
    {
        if (! is_string($data)) {
            return $this->attributes['data'] = $data->toJson();
        }

        return $this->attributes['data'] = $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function getDataAttribute($data)
    {
        return json_decode($data, true);
    }

    /**
     * @param int $productId
     * @param string $channel
     * @param string $action
     * @param $data
     * @param int|null $syncJobLogId
     * @return static
     * @throws \Exception
     */
    public static function log(int $productId, string $channel, string $action, $data, int $syncJobLogId = null)
    {
        self::validate($productId, $channel, $action, $data);

        $attributes = [
            'product_id' => $productId,
            'channel'    => $channel,
            'action'     => $action,
            'data'       => $data,
        ];

        if ($syncJobLogId) {
            $attributes['sync_job_log_id'] = $syncJobLogId;
        }

        return parent::create($attributes);
    }

    /**
     * Must create using "log" method.
     *
     * @param array $attributes
     * @return static
     */
    public static function create(array $attributes = [])
    {
        dd('Use ChangeLog::log() to create this model.');

        return parent::create();
    }

    /**
     * @param int $productId
     * @param string $channel
     * @param string $action
     * @param $data
     * @throws \Exception
     */
    protected static function validate(int $productId, string $channel, string $action, $data)
    {
        $possibleActions = [static::ACTION_REMOVED, static::ACTION_CREATED, static::ACTION_UPDATED];
        $possibleChannels = [CHANNEL_AMAZON, CHANNEL_CSV_IMPORT, CHANNEL_EBAY, CHANNEL_MERCHANT_PORTAL];

        if (! Product::find($productId)) {
            throw new ModelNotFoundException;
        }

        if (! in_array($channel, $possibleChannels)) {
            throw new \Exception('Invalid channel argument. Please use class constants');
        }

        if (! in_array($action, $possibleActions)) {
            throw new \Exception('Invalid action argument. Please use class constants');
        }

        if (! ($data instanceof CreatedDataType || $data instanceof RemovedDataType || $data instanceof UpdatedDataType)) {
            throw new \Exception('Invalid data type argument. Please use one of the ChangeActionLog classes.');
        }
    }
}
