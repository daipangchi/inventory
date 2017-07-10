<?php

namespace App\Http\Controllers;

class ErrorsController extends Controller
{
    /**
     * Return "Page not found" page.
     * 
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function notFound()
    {
        return response(view('errors.404'), 404);
    }
}
