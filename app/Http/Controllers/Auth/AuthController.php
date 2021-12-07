<?php


namespace App\Http\Controllers\Auth;

use App\Helpers\ResultGenerate;
use App\Helpers\StringHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Ucaller\Ucaller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function PhoneValidation(Request $request)
    {

        $phone = $request->phone;

        $ucaller = new Ucaller();
        $initCall = $ucaller->InitCall($phone);
        $code = $initCall['code'];

//        $code = '1111';

        session()->put('confirmationCode', $code);
        session()->put('clientPhone', $phone);
        return ResultGenerate::Success('Введите последние 4 цифры входящего звонка');
    }

    public function CheckConfirmationCode(Request $request)
    {
        $inputCode = $request->confirmationCode;
        $confirmationCode = session()->get('confirmationCode');
        $clientPhone = session()->get('clientPhone');

        if ((int)$inputCode === (int)$confirmationCode) {
            $user = User::where('phone', $clientPhone)->first();
            if (!$user) {
                $user = self::FastRegistrationUserByPhone($clientPhone);
            }
            Auth::login($user);
            session()->flash('execFunction', $request->execFunction);
            return ResultGenerate::Success('Вы авторизовались');
        }

        return ResultGenerate::Error('Не верный код');
    }

    public function Logout()
    {
        Auth::logout();
        return ResultGenerate::Success();
    }

    public static function FastRegistrationUserByPhone($phone)
    {
        $phone = preg_replace("/[^0-9]/", '', $phone);
        $randomPassword = Str::random();
        return User::create([
            'name' => 'Новый пользователь',
            'email' => $phone,
            'phone' => $phone,
            'password' => $randomPassword,
        ]);
    }

    public static function UpdateUserName(User $user, string $newName)
    {
        $user->name = $newName;
        $user->save();
    }
}
