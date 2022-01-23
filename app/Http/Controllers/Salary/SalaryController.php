<?php

namespace App\Http\Controllers\Salary;

use App\Helpers\ResultGenerate;
use App\Models\Calendar;
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
        $offsetMonth = request()->offsetMonth ?? 0;
        $shifts = Calendar::where('date', '>=', now()->addMonth($offsetMonth)->startOfMonth()->format('Y-m-d'))
            ->where('date', '<=', now()->addMonth($offsetMonth)->endOfMonth()->format('Y-m-d'))
            ->get();

        $shiftsGroupByDay = [];
        foreach ($shifts as $shift) {
            $shiftsGroupByDay[$shift->date][] = $shift;
        }
        $employees = User::Employees();
        return view('arm.salary.calendar.index', compact('employees', 'shiftsGroupByDay', 'offsetMonth'));
    }

    public function AddShift()
    {
        $employeeId = request()->post('employeeId');
        $date = request()->post('date');
        $startShift = request()->post('startShift');
        $endShift = request()->post('endShift');

        if (empty($startShift) || empty($endShift)) {
            return ResultGenerate::Error('Запоните время смены!');
        }

        $shift = Calendar::create([
            'user_id' => $employeeId,
            'date' => $date,
            'start_shift' => $startShift,
            'end_shift' => $endShift,
        ]);

        return ResultGenerate::Success('', $shift);
    }

    function DeleteShift()
    {
        $shift = Calendar::findOrFail(request()->post('shiftId'));
        $shift->delete();
        return ResultGenerate::Success();
    }

    public function DayDetail()
    {
        $date = request()->post('date');
        return view('arm.salary.calendar.detail');
    }
}
