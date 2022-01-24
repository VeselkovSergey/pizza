<?php

namespace App\Http\Controllers\Salary;

use App\Helpers\ResultGenerate;
use App\Models\Calendar;
use App\Models\Categories;
use App\Models\User;
use App\Services\Telegram\Telegram;

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

        self::SendTelegram($employeeId, 'AddShift', $shift);

        return ResultGenerate::Success('', $shift);
    }

    function DeleteShift()
    {
        $shift = Calendar::findOrFail(request()->post('shiftId'));
        self::SendTelegram($shift->user_id, 'DeleteShift', $shift);
        $shift->delete();
        return ResultGenerate::Success();
    }

    public function DayDetail()
    {
        $date = request()->post('date');
        return view('arm.salary.calendar.detail');
    }

    private function SendTelegram($employeeId, $type, $shift)
    {
        $employee = User::find($employeeId);

        if (empty($employee) && empty($employee->telegram_chat_id)) {
            return false;
        }

        $winText = 'Тебе повезло!';
        $shiftText = 'У тебя новая смена:';
        if ($type === 'DeleteShift') {
            $winText = 'Вынужден тебя огорчить!';
            $shiftText = 'Тебе сняли смену:';
        }

        $message = '<b>' . $employee->name . '! '.$winText.'</b>' . PHP_EOL;
        $message .= PHP_EOL;

        $message .= '<i>'.$shiftText.'</i> ' . PHP_EOL;

        $message .= '<i>' . $shift->date . '</i> ' . PHP_EOL;
        $message .= PHP_EOL;

        $message .= '<i>C ' . $shift->start_shift . ' до ' . $shift->end_shift . '</i> ' . PHP_EOL;

        $telegram = new Telegram();
        return $telegram->sendMessage($message, $employee->telegram_chat_id);
    }
}
