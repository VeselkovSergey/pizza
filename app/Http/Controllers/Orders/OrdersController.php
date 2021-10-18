<?php


namespace App\Http\Controllers\Orders;

use App\Helpers\ArrayHelper;
use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Products\ProductsController;
use App\Models\ProductModifications;
use App\Models\Products;
use App\Services\Telegram\Telegram;
use Illuminate\Http\Request;
use Monolog\Handler\TelegramBotHandler;

class OrdersController extends Controller
{
    public function Create(Request $request)
    {
        $basket = json_decode($request->basket);
        $clientInformation = json_decode($request->clientInformation);
        $clientInformation->clientPhone = auth()->user()->phone;
        $modificationsId = [];
        $amountProductModificationInOrder = [];
        foreach ($basket as $product) {
            $productModification = $product->data->modification;
            $productModificationId = $productModification->id;
            $amountProductModification = $product->amount;
            $amountProductModificationInOrder[$productModificationId] = $amountProductModification;
            $modificationsId[] = $productModificationId;
        }
        $productsModifications = ProductModifications::whereIn('id', $modificationsId)->get();

        $orderFullInformationAboutOrderedProduct = [];
        $orderSum = 0;
        foreach ($productsModifications as $productsModification) {
            $dataModification = (object)[
                'id' => $productsModification->id,
                'title' => $productsModification->Product->title . ' - ' . $productsModification->Modification->title . ' - ' . $productsModification->Modification->value . ' - ' . $productsModification->selling_price . ' ₽',
                'amount' => $amountProductModificationInOrder[$productsModification->id],
            ];
            $orderSum = $orderSum + ($productsModification->selling_price * $amountProductModificationInOrder[$productsModification->id]);
            $orderFullInformationAboutOrderedProduct[] = $dataModification;
        }

        $orderFullInformation = (object)[
            'products' => $orderFullInformationAboutOrderedProduct,
            'orderSum' => $orderSum,
            'clientInformation' => $clientInformation,
        ];

        $this->SendTelegram($orderFullInformation);

        return ResultGenerate::Success('Заказ принят. Мы скоро свяжимся с вами.');
    }

    private function SendTelegram($orderFullInformation) {

        $allProductsInOrder = $orderFullInformation->products;
        $clientInformation = $orderFullInformation->clientInformation;

        $products = '';
        foreach ($allProductsInOrder as $key => $product) {
            $products .= $key + 1 . '. ' .$product->title . ' - ' . $product->amount . PHP_EOL;
        }

        $message = '<b>Клиент:</b>' . PHP_EOL;
        $message .= '<i>Имя:</i> ' . $clientInformation->clientName . PHP_EOL;
        $message .= '<i>Телефон:</i> +' . $clientInformation->clientPhone . PHP_EOL;
        $message .= '<i>Оплата:</i> ' . ($clientInformation->typePayment[0] === true ? 'Карта' : 'Наличные') . PHP_EOL;
        $message .= '<i>Адрес:</i> ' . $clientInformation->clientAddressDelivery . PHP_EOL;
        $message .= '<i>Комментарий:</i> ' . $clientInformation->clientComment . PHP_EOL;
//        $message .= '<i>Промокод:</i> ' . $clientInformation->clientPromoCode . PHP_EOL;
        $message .= PHP_EOL;
        $message .= '<b>Заказ:</b>' . PHP_EOL;
        $message .= $products;
        $message .= '<i>Итого:</i> ' . $orderFullInformation->orderSum . ' ₽' . PHP_EOL;

        $telegram = new Telegram();
        $telegram->sendMessage($message, '-1001538892405');
//        $telegram->sendMessage($message, '267236435');
    }
}
