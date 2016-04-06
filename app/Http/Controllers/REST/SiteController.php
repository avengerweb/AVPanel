<?php

namespace App\Http\Controllers\REST;

use App\Models\Site;
use App\Models\SshCommands;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Site::forMe()->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Site::$rules);

        $site = new Site();
        $site->name = $request->get("name");
        $site->url = $request->get("url");
        $site->access_log = $request->has("access_log");
        $site->error_log = $request->has("error_log");
        $site->status = Site::STATUS_ENABLED;
        $site->user_id = \Auth::user()->id;
        $site->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function configure($id, $isFirst = true) {
        $site = Site::forMe()->with("user")->findOrFail($id);

        // Connect to ssh
        $nginxConfiguration = config("panel.nginx");
        $connection = \SSH::connection($nginxConfiguration['connection']);

        $commands = new SshCommands($connection);

        $vhostsDir = $nginxConfiguration['vhosts'] . "/" . $site->user->getNick();

        // Check user vhost directory if not exists
        if (!$connection->exists($vhostsDir))
            $commands->createDirectory($nginxConfiguration['vhosts'], [$site->user->getNick()]);

        $userDir = $nginxConfiguration['workdir'] . $site->user->getNick();
        $siteDir = $userDir . "/www/" . $site->directory;

        // Check user exists in system
        if (!$commands->userExists($site->user->getNick()))
            $commands->addUser($site->user->getNick(), $nginxConfiguration['workdir'], $nginxConfiguration['group']);

        // Check www and logs directory exists if not crate it
        if (!$connection->exists($userDir . "www") || !$connection->exists($userDir . "logs")) {
            $commands->createDirectory($userDir, ["www", "logs"]);
            $commands->chown($site->user->getNick(), $nginxConfiguration['group'], $userDir . "/logs");
        }

        // Check site directory exists if not crate it
        if (!$connection->exists($siteDir)) {
            $commands->createDirectory($userDir . "/www/", [$site->directory]);
            $commands->chown($site->user->getNick(), $nginxConfiguration['group'], $siteDir);
        }

        // Create first index.html if not exists
        if ($isFirst && !$connection->exists($siteDir . "/index.html")) {
            $connection->putString($siteDir . "/index.html", \View::make("configs.default", ["site" => $site]));
            $commands->chown($site->user->getNick(), $nginxConfiguration['group'], $siteDir . "/index.html");
        }

        // Update nginx configuration
        $connection->putString($vhostsDir . "/" . $site->directory . ".conf", \View::make("configs.nginx", ["site" => $site]));

        // Restart server
        $connection->run("service nginx restart", function($out) {

        });

    }
}
