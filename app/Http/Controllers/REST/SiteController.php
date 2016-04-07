<?php

namespace App\Http\Controllers\REST;

use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

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
        
        $site->configure(true);
        
        $site->save();
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
        $site = Site::forMe()->findOrFail($id);
        $this->validate($request, $site->updateRules());

        $site->name = $request->get("name");
        $site->url = $request->get("url");
        $site->access_log = $request->has("access_log");
        $site->error_log = $request->has("error_log");
        $site->status = $request->get("status");
        
        $site->configure(false, $site->getOriginal());
        
        $site->save();

        return $site;
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
}
