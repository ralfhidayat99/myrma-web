<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TerlambatController extends Controller
{
    function index()
    {
        $data['menu'] = 'Terlambat';


        return view('pages.terlambat.index', $data);
    }
}
