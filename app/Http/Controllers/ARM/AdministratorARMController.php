<?php


namespace App\Http\Controllers\ARM;

use App\Http\Controllers\Controller;

class AdministratorARMController extends Controller
{
    public function Index()
    {
        return view('arm.administration.index');
    }
}
