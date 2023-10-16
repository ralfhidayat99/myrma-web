<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CutiController extends Controller
{
    function index()
    {
        $data['menu'] = 'Cuti';


        return view('pages/cuti/cuti', $data);
    }
}
