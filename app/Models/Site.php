<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\Site
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property boolean $status
 * @property string $directory
 * @property string $slug
 * @property integer $user_id
 * @property boolean $access_log
 * @property boolean $error_log
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Site whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Site whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Site whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Site whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Site whereDirectory($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Site whereAdminEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Site whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Site whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Site whereAccessLog($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Site whereErrorLog($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Site whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Site whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Site findOrFail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Site forMe()
 * @mixin \Eloquent
 * @property-read \App\User $user
 */
class Site extends Model
{
    //Statues
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    // Unused
    const STATUS_DISABLED_BY_PLATFORM = 2;

    // Validation rules
    public static $rules = [
        "name" => "required|max:255",
        "url" => "required|unique:sites|alpha_dash|max:255",
    ];

    /**
     * Condition for only current user websites
     *
     * @param $query
     * @return mixed
     */
    public function scopeForMe($query) {
        return $query->whereUserId(\Auth::user()->id);
    }

    /**
     * Setting url
     *
     * @param  string  $value
     * @return string
     */
    public function setUrlAttribute($value)
    {
        $this->attributes['url'] = Str::lower($value);
        $this->attributes['directory'] = $this->attributes['url'];
    }

    /**
     * Relation to user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}
