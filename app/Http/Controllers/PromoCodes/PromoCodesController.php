<?php

namespace App\Http\Controllers\PromoCodes;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Products\ProductsController;
use App\Models\Products;
use App\Models\PromoCodes;
use App\Models\PromoCodesUsersUsed;
use Illuminate\Support\Str;

class PromoCodesController extends Controller
{
    public function CheckPromoCodeRequest()
    {
        $promoCodeTitle = request()->post('promoCode');
        $promoCode = PromoCodes::where('title', $promoCodeTitle)->where('active', 1)->first();
        //  нашли промокод
        if ($promoCode) {
            $promoCode = self::CheckPromoCode($promoCode);
            if ($promoCode !== false) {
                $promoCode->conditions = json_decode($promoCode->conditions);
                return ResultGenerate::Success('', $promoCode);
            }
        }
        return ResultGenerate::Error();
    }

    public static function CheckPromoCode(PromoCodes $promoCode, $userId = 0)
    {
        //  промокод можно еще раз использовать
        if ($promoCode->amount_used < $promoCode->amount) {

            if ($promoCode->user_limit === 0) {
                return $promoCode;
            } else {
                if ($userId === 0) {
                    $userId = auth()->user()->id;
                }
                $userUsage = PromoCodesUsersUsed::where('user_id', $userId)->where('promo_code_id', $promoCode->id)->count('id');
                if ($userUsage < $promoCode->user_limit) {
                    return $promoCode;
                }
            }

        }
        return false;
    }

    public function AllPromoCodesPage()
    {
        $promoCodes = PromoCodes::all();
        return view('arm.administration.promo-codes.index', compact('promoCodes'));
    }

    public function CreatePromoCodePage()
    {
        $products = Products::all();
        return view('arm.administration.promo-codes.create', compact('products'));
    }

    public function CreatePromoCodeRequest()
    {
        $title = request()->post('title');
        $description = request()->post('description');
        $startDate = request()->post('start_date');
        $endDate = request()->post('end_date');
        $amount = (int)request()->post('amount');
        $userLimit = (int)request()->post('user_limit');

        $generalDiscountPercent = (int)request()->post('generalDiscountPercent');
        $generalDiscountSum = (int)request()->post('generalDiscountSum');

        $everyDiscountPercent = (int)request()->post('everyDiscountPercent');
        $everyDiscountSum = (int)request()->post('everyDiscountSum');
        $everySalePrice = (int)request()->post('everySalePrice');
        $everyReiterationsCounts = (int)request()->post('everyReiterationsCounts');

        $modificationsRaw = request()->post('modifications');
        $modifications = [];

        //  если не установлена глобальная скидка на заказ
        if (empty($generalDiscountPercent) && empty($generalDiscountSum)) {
            foreach ($modificationsRaw as $id => $modificationRaw) {
                if ($modificationRaw !== 'false') {
                    $modifications[] = $id;
                }
            }
        }

        if (!empty($modifications) && (empty($everyDiscountPercent) && empty($everyDiscountSum) && empty($everySalePrice))) {
            //   если модификации не пустые и не выбраны скидки для модификаций
            return ResultGenerate::Error('Модификации не выбраны');
        } else if (empty($generalDiscountPercent) && empty($generalDiscountSum) && empty($modifications)) {
            //  если пустые модификации и пустые глобальные скидки на заказа
            return ResultGenerate::Error('Не верный формат промокода. Сделайте промо на модификации или на общий заказ!');
        }

        $promoCode = [
            'title' => $title,
            'conditions' => json_encode((object)[
                'every' => (object)[
                    'productModifications' => $modifications,
                    'reiterationsCounts' => $everyReiterationsCounts,
                    'discountPercent' => !empty($everyDiscountPercent) ? $everyDiscountPercent : null,
                    'discountSum' => !empty($everyDiscountSum) ? $everyDiscountSum : null,
                    'salePrice' => !empty($everySalePrice) ? $everySalePrice : null,
                ],
                'general' => (object)[
                    'discountPercent' => !empty($generalDiscountPercent) ? $generalDiscountPercent : null,
                    'discountSum' => !empty($generalDiscountSum) ? $generalDiscountSum : null,
                ]
            ]),
            'description' => $description,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount' => $amount,
            'user_limit' => $userLimit,
            'amount_used' => 0,
            'active' => 1,
        ];

        if (PromoCodes::where('title', $title)->first()) {
            return ResultGenerate::Error('Промокод с таким название уже существует!');
        }
        PromoCodes::create($promoCode);
        return ResultGenerate::Success('Промокод успешно создан');
    }

    public function ChangeActivePromoCode()
    {
        $promoCode = PromoCodes::find(request()->post('promoCodeId'));
        $promoCode->active = request()->post('promoCodeActive') === 'true' ? 1 : 0;
        $promoCode->save();
        return ResultGenerate::Success();
    }

    public static function GenerateSale($percent = 0, $description = '')
    {
        $title = strtoupper(Str::random(10));
        $promoCode = [
            'title' => $title,
            'conditions' => json_encode((object)[
                'every' => (object)[
                    'productModifications' => null,
                    'reiterationsCounts' => null,
                    'discountPercent' => null,
                    'discountSum' => null,
                    'salePrice' => null,
                ],
                'general' => (object)[
                    'discountPercent' => $percent,
                    'discountSum' => null,
                ]
            ]),
            'description' => $description,
            'start_date' => now()->addDays(-1),
            'end_date' => now()->addMonth(1),
            'amount' => 1,
            'user_limit' => 1,
            'amount_used' => 0,
            'active' => 1,
        ];

        if (PromoCodes::where('title', $title)->first()) {
            return false;
        }
        PromoCodes::create($promoCode);
        return $title;
    }
}
