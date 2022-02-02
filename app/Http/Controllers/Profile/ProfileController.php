<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function Index()
    {
        if (!auth()->check()) {
            return redirect(route('home-page'));
        }

        return view('profile.index');
    }

    public function Orders()
    {
        if (!auth()->check()) {
            return redirect(route('home-page'));
        }

        $userOrders = auth()->user()->Orders()->orderByDesc('id')->get();
        return view('profile.orders', compact('userOrders'));
    }
}