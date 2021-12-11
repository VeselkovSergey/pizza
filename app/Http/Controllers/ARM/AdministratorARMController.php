<?php


namespace App\Http\Controllers\ARM;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\OrdersController;
use App\Models\Orders;
use App\Models\User;

class AdministratorARMController extends Controller
{
    public function Index()
    {
        return view('arm.administration.index');
    }

    public function Users()
    {
        $users = User::all();
        return view('arm.administration.users.index', compact('users'));
    }

    public function UserOrders($userId)
    {
        $user = User::find($userId);
        $orders = $user->Orders;
        return view('arm.administration.users.orders', compact('user', 'orders'));
    }

    public function Orders()
    {
        $orders = Orders::all();
        return view('arm.administration.orders.index', compact('orders'));
    }
}
