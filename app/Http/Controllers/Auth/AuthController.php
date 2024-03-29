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

        $isPersonal = User::where('phone', $phone)->whereIn('role_id', [999, 777])->first();

        if ($isPersonal) {
            $code = $isPersonal->role_id === 999 ? '9999' : '1111';
        } else {
            $ucaller = new Ucaller();
            $initCall = $ucaller->InitCall($phone);
            $code = $initCall['code'];
        }

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

    public function AllSessions()
    {
        $sessionsArr = [];
        $sessions = \Storage::disk('sessions')->allFiles();
        foreach ($sessions as $session) {
            if ($session !== '.gitignore') {
                $data = file_get_contents(storage_path('framework/sessions/' . $session));

                if (empty(unserialize($data)['clientPhone'])) {
                    continue;
                }

                $phone = unserialize($data)['clientPhone'];
                if ($phone === \auth()->user()->phone) {
                    $sessionsArr[] = unserialize($data);
                }
            }
        }
        return view('arm.administration.users.sessions', compact('sessionsArr'));
    }

    public function LogoutAllDevices()
    {
        $sessions = \Storage::disk('sessions')->allFiles();
        foreach ($sessions as $session) {
            if ($session !== '.gitignore') {
                $data = file_get_contents(storage_path('framework/sessions/' . $session));

                if (empty(unserialize($data)['clientPhone'])) {
                    continue;
                }

                $phone = unserialize($data)['clientPhone'];
                if ($phone === \auth()->user()->phone) {
                    unlink(storage_path('framework/sessions/' . $session));
                }
            }
        }
        return ResultGenerate::Success();
    }

    public static function FastRegistrationUserByPhone($phone)
    {
        $phone = preg_replace("/[^0-9]/", '', $phone);
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            $randomPassword = Str::random();
            $user = User::create([
                'name' => 'Новый пользователь',
                'email' => $phone,
                'phone' => $phone,
                'password' => $randomPassword,
            ]);
        }
        return $user;
    }

    public static function UpdateUserName(User $user, string $newName)
    {
        $user->name = $newName;
        $user->save();
    }

    public static function GetUserById($userId)
    {
        return User::find($userId);
    }

    public static function SaveChanges(User $user, array|object $data)
    {
        foreach ($data as $title => $value) {
            $user->$title = $value;
        }
        $user->save();
        return $user;
    }
}
