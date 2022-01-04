<?php

namespace App\Http\Controllers\PromoCodes;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Products\ProductsController;
use App\Models\PromoCodes;

class PromoCodesController extends Controller
{
    public function CheckPromoCodeRequest()
    {
        $promoCodeTitle = request()->post('promoCode');
        $promoCode = PromoCodes::where('title', $promoCodeTitle)->first();
        if ($promoCode) {
            $promoCode->conditions = json_decode($promoCode->conditions);
           return ResultGenerate::Success('', $promoCode);
        }

        return ResultGenerate::Error();
    }

    public function AllPromoCodesPage()
    {
        $promoCodes = PromoCodes::all();

        return view('arm.administration.promo-codes.index', compact('promoCodes'));
    }

    public function CreatePromoCodePage()
    {
        $products = ProductsController::ALlProducts();

        return view('arm.administration.promo-codes.create', compact('products'));
    }

    public function CreatePromoCodeRequest()
    {
        $title = request()->post('title');
        $description = request()->post('description');
        $startDate = request()->post('start_date');
        $endDate = request()->post('end_date');
        $amount = (int)request()->post('amount');

        $generalDiscountPercent = (int)request()->post('generalDiscountPercent');
        $generalDiscountSum = (int)request()->post('generalDiscountSum');

        $everyDiscountPercent = (int)request()->post('everyDiscountPercent');
        $everyDiscountSum = (int)request()->post('everyDiscountSum');
        $everySalePrice = (int)request()->post('everySalePrice');
        $everyReiterationsCounts = (int)request()->post('everyReiterationsCounts');

        $modificationsRaw = request()->post('modifications');
        $modifications = [];
        foreach ($modificationsRaw as $id => $modificationRaw) {
            if ($modificationRaw !== 'false') {
                $modifications[] = $id;
            }
        }

        if (empty($modifications) && (!empty($everyDiscountPercent) || !empty($everyDiscountSum) || !empty($everySalePrice))) {
            return ResultGenerate::Error('Модификации не выбраны');
        } else if (empty($generalDiscountPercent) && empty($generalDiscountSum)) {
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
            'amount_used' => 0,
        ];

        if (PromoCodes::where('title', $title)->first()) {
            return ResultGenerate::Error('Промокод с таким название уже существует!');
        }
        PromoCodes::create($promoCode);
        return ResultGenerate::Success('Промокод успешно создан');
    }
}