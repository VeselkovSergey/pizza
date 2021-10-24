<?php


namespace App\Http\Controllers\ARM;

use App\Http\Controllers\Controller;

class ChefARMController extends Controller
{
    public function Index()
    {
        return view('arm.chef.index');
    }
}
