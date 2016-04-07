<?php
/**
 * Created by PhpStorm.
 * User: avenger-web
 * Date: 05.04.16
 * Time: 21:21
 */

namespace App\Models;


use Collective\Remote\ConnectionInterface;
use Illuminate\Support\Str;

class SshCommands
{
    private $connection = null;

    /**
     * SshCommands constructor.
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Check existing user in system
     *
     * @param $name
     * @return bool
     */
    public function userExists($name) {
        $exists = false;

        $this->connection->run("id " . $name, function($out) use (&$exists) {
            $exists = Str::contains($out, ["uid", "gid"]);
        });

        return $exists;
    }

    /**
     * Add new user
     *
     * @param $name
     * @param string $home
     * @param $additionGroups
     */
    public function addUser($name, $home = "/home/", $additionGroups) {
        $command = "useradd -m -b ". $home;
        
        if ($additionGroups)
            $command .= "  -G  " . $additionGroups;

        $command .= " " . $name;
        
        $this->connection->run($command);
    }

    /**
     * Change file/directory group or owner
     *
     * @param $user
     * @param $group
     * @param $path
     */
    public function chown($user, $group, $path)
    {
        $this->connection->run("chown $user:$group $path");
    }

    /**
     * Create new directories
     *
     * @param $rootDir
     * @param array $directories
     */
    public function createDirectory($rootDir, array $directories)
    {
        foreach ($directories as &$value)
            $value = "mkdir " . $value;

        $this->connection->run(array_merge(["cd " . $rootDir], $directories));
    }
}