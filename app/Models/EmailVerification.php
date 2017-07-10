<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailVerification
 *
 * @property integer $id
 * @property string $email
 * @property string $token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailVerification whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailVerification whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailVerification whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailVerification whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailVerification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EmailVerification extends Model
{
    public $fillable = ['email', 'token'];

    //
}
