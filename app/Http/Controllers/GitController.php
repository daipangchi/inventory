<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Jobs\GitDeploymentJob;
use Config;
use Illuminate\Http\Request;

class GitController extends Controller
{
    function deploy(Request $request)
    {
        $authenticated = Config::get('git.secret_access_token') !== $request->get('sat');

        if ($authenticated) {
            dispatch(new GitDeploymentJob());

            return 'good';
        }
        return response('bad', 403);
    }
}
