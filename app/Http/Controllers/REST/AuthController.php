<?php

namespace App\Http\Controllers\REST;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Auth users and return token
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = \Validator::make($request->all(), [
           "email" => "required|email",
           "password" => "required",
        ]);

        $errors = [];
        if ($validator->passes()) {
            if (!\Auth::attempt([
                "email" => $request->get("email"),
                "password" => $request->get("password")
            ]))
                $errors[] = "Not right login or password!";
        } else
            $errors += $validator->getMessageBag()->getMessages();

        return count($errors) ?
            ["success" => false, "errors" => $errors] :
            ["success" => true, "token" => \Crypt::encrypt(\Session::getId())];
    }
}
