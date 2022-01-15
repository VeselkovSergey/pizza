<?php

namespace App\Http\Controllers\Salary;

use App\Models\User;

class SalaryController
{
    public function Index()
    {
        return view('arm.salary.index');
    }

    public function Employees()
    {
        $usersEmployees = User::Employees();
        return view('arm.salary.employees.index', compact('usersEmployees'));
    }

    public function EmployeeCard()
    {
        $employee = User::find(request()->employeeId);
        return view('arm.salary.employees.card', compact('employee'));
    }

    public function Save()
    {
        $employee = User::find(request()->employeeId);
        dd($employee);
    }

    public function Calendar()
    {
        return view('arm.salary.calendar.index');
    }

    public function DayDetail()
    {
        $date = request()->post('date');
        return view('arm.salary.calendar.detail');
    }
}