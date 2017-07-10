<?php

namespace App\Http\Controllers;

class ShipmentsController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        return view('pages.shipments.index');
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return view('pages.shipments.create');
    }
}
