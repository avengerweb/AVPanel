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
     * Returning validate rules for update request
     * 
     * @return array
     */
    public function updateRules() {
        return [
            "status" => "required|in:" . Site::STATUS_DISABLED . "," . Site::STATUS_ENABLED,
            "url" => "required|alpha_dash|max:255|unique:sites,url," . $this->id,
        ] + self::$rules;
    }

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

    /**
     * Create configuration for site
     *
     * @param bool $isFirst
     * @param null $original
     */
    public function configure($isFirst = false, $original = null) {
        // Connect to ssh
        $nginxConfiguration = config("panel.nginx");
        $connection = \SSH::connection($nginxConfiguration['connection']);

        $commands = new SshCommands($connection);

        $vhostsDir = $nginxConfiguration['vhosts'] . "/" . $this->user->getNick();

        // Check user vhost directory if not exists
        if (!$connection->exists($vhostsDir))
            $commands->createDirectory($nginxConfiguration['vhosts'], [$this->user->getNick()]);

        $userDir = $nginxConfiguration['workdir'] . $this->user->getNick();
        $siteDir = $userDir . "/www/" . $this->directory;

        // Check user exists in system  
        if (!$commands->userExists($this->user->getNick()))
            $commands->addUser($this->user->getNick(), $nginxConfiguration['workdir'], $nginxConfiguration['group']);

        // Check www and logs directory exists if not crate it
        if (!$connection->exists($userDir . "www") || !$connection->exists($userDir . "logs")) {
            $commands->createDirectory($userDir, ["www", "logs"]);
            $commands->chown($this->user->getNick(), $nginxConfiguration['group'], $userDir . "/logs");
        }

        $dirChanged = $original && isset($original["directory"]) && $original["directory"]
            && !hash_equals($this->directory, $original["directory"]);

        // Check site directory exists if not crate it or rename
        if (!$connection->exists($siteDir)) {
            // check availability the old directory
            if ($dirChanged && $connection->exists($userDir . "/www/" . $original['directory'])) {
                    $connection->rename($userDir . "/www/" . $original['directory'], $siteDir);
            } else {
                $commands->createDirectory($userDir . "/www/", [$this->directory]);
                $commands->chown($this->user->getNick(), $nginxConfiguration['group'], $siteDir);
            }
        }

        // Create first index.html if not exists
        if ($isFirst && !$connection->exists($siteDir . "/index.html")) {
            $connection->putString($siteDir . "/index.html", \View::make("configs.default", ["site" => $this]));
            $commands->chown($this->user->getNick(), $nginxConfiguration['group'], $siteDir . "/index.html");
        }

        if ($dirChanged && $connection->exists($vhostsDir . "/" . $original['directory'] . ".conf")) {
            $connection->delete($vhostsDir . "/" . $original['directory'] . ".conf");
        }

        $confFile = $vhostsDir . "/" . $this->directory . ".conf";

        // Writing nginx configuration
        if ($this->status == Site::STATUS_ENABLED)
            $connection->putString($confFile, \View::make("configs.nginx", ["site" => $this]));
        else // Remove if disabled
            if ($connection->exists($confFile))
                $connection->delete($confFile);

        // Restart server
        $connection->run("service nginx reload");
    }
}
