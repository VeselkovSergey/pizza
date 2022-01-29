<?php


namespace App\Http\Controllers\Orders;

use App\Helpers\ArrayHelper;
use App\Helpers\ResultGenerate;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Payments\PaymentsController;
use App\Http\Controllers\Products\ProductsController;
use App\Http\Controllers\PromoCodes\PromoCodesController;
use App\Models\Orders;
use App\Models\OrdersProductsStatusLogs;
use App\Models\OrdersStatusLogs;
use App\Models\Payments;
use App\Models\ProductModifications;
use App\Models\Products;
use App\Models\ProductsModificationsInOrders;
use App\Models\PromoCodes;
use App\Models\PromoCodesUsersUsed;
use App\Models\UsedDevices;
use App\Models\User;
use App\Services\Pusher\Pusher;
use App\Services\SberBank\SberBank;
use App\Services\Telegram\Telegram;
use Illuminate\Http\Request;
use Monolog\Handler\TelegramBotHandler;

class OrdersController extends Controller
{
    public function Create(Request $request)
    {
        $basket = json_decode($request->basket);
        $clientInformation = self::CleanClientInformation(json_decode($request->clientInformation));
        $clientInformation->clientPhone = auth()->user()->IsManager() ? $clientInformation->clientPhone : auth()->user()->phone;

        $orderAmount = $request->orderAmount;

        $paymentType = $clientInformation->typePayment[0];

        $clientInformation->clientPhone = preg_replace("/[^0-9]/", '', $clientInformation->clientPhone);

        $orderId = $request->orderId !== 'null' ? (int)$request->orderId : false;

        $user = User::where('phone', $clientInformation->clientPhone)->first();

        if (!$user) {
            $user = AuthController::FastRegistrationUserByPhone($clientInformation->clientPhone);
        }

        if ($user->UserIsAdmin()) {
            $orderAmount = (int)($orderAmount / 2);
        }

        $editOrder = false;
        if (!empty($orderId)) {
            $order = Orders::find($orderId);
            if (empty($order)) {
                return ResultGenerate::Error('Произошла ошибка! Создайте новый заказ. Этот переведите в статус ОТКАЗ');
            }
            $order->client_raw_data = json_encode($clientInformation);
            $order->products_raw_data = json_encode($basket);
            $order->all_information_raw_data = json_encode($request->all());
            $order->order_amount = $orderAmount;
            $order->save();
            ProductsModificationsInOrders::where('order_id', $orderId)->delete();

            $editOrder = true;

            $flashMessage = 'Заказ обновлен. Не забудь обновить страницу с заказом.';
        } else {
            $newOrder = Orders::create([
                'user_id' => $user->id,
                'status_id' => Orders::STATUS_TEXT['clientCreateOrder'],
                'client_raw_data' => json_encode($clientInformation),
                'products_raw_data' => json_encode($basket),
                'all_information_raw_data' => json_encode($request->all()),
                'order_amount' => $orderAmount,
            ]);
            $order = $newOrder;
            $orderId = $order->id;
            self::ChangeStatus($order, Orders::STATUS_TEXT['newOrder']);

            if (!auth()->user()->IsManager()) {
                $screenWidth = $request->screenWidth;
                $screenHeight = $request->screenHeight;
                $userAgent = $request->userAgent;
                $deviceInfo = (object)[
                    'screenWidth' => $screenWidth,
                    'screenHeight' => $screenHeight,
                    'userAgent' => $userAgent,
                ];

                UsedDevices::create([
                    'device_info' => json_encode($deviceInfo),
                    'order_id' => $orderId,
                ]);
            }

            $flashMessage = 'Заказ принят. Мы скоро свяжемся с вами.';

            $promoCodeTitle = $clientInformation->clientPromoCode;
            $promoCode = PromoCodes::where('title', $promoCodeTitle)->first();
            if ($promoCode) {
                if (PromoCodesController::CheckPromoCode($promoCode, $user->id) !== false) {
                    $promoCode->amount_used = $promoCode->amount_used + 1;
                    $promoCode->save();

                    PromoCodesUsersUsed::create([
                        'user_id' => $user->id,
                        'promo_code_id' => $promoCode->id,
                        'order_id' => $orderId,
                    ]);

                }
            }

        }

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
        $totalOrderAmount = 0;

        foreach ($productsModifications as $productsModification) {

            $dataModification = (object)[
                'id' => $productsModification->id,
                'title' => $productsModification->Product->title . ' - ' . $productsModification->Modification->title . ' - ' . $productsModification->Modification->value . ' - ' . $productsModification->selling_price . ' ₽',
                'amount' => $amountProductModificationInOrder[$productsModification->id],
            ];
            $totalOrderAmount += ($productsModification->selling_price * $amountProductModificationInOrder[$productsModification->id]);
            $orderFullInformationAboutOrderedProduct[] = $dataModification;

            $newProductModificationInNewOrder = ProductsModificationsInOrders::create([
                'order_id' => $orderId,
                'status_id' => ProductsModificationsInOrders::STATUS_TEXT['create'],
                'product_modification_id' => $productsModification->id,
                'product_modification_amount' => $amountProductModificationInOrder[$productsModification->id]
            ]);
            self::OrderProductChangeStatus($newProductModificationInNewOrder, ProductsModificationsInOrders::STATUS_TEXT['new']);
        }

        $order->total_order_amount = $totalOrderAmount;
        $order->save();

        $orderFullInformation = (object)[
            'products' => $orderFullInformationAboutOrderedProduct,
            'orderAmount' => $orderAmount,
            'totalOrderAmount' => $totalOrderAmount,
            'clientInformation' => $clientInformation,
            'orderId' => $orderId,
            'order' => $order,
        ];

        $result = $this->SendTelegram($orderFullInformation, $editOrder);
        $result = json_decode($result);
        if ($result && $result->ok === true) {
            $order->order_telegram_message_id = $result->result->message_id;
            $order->save();
        }

        AuthController::UpdateUserName($user, $clientInformation->clientName);

//        $paymentTypeString = $paymentType ? 'bank' : 'cash';
//
//        $payment = PaymentsController::CreatePayment($order, $orderAmount, $paymentTypeString);
//
//        if ($paymentType) {
//            $paymentService = new SberBank();
//            $paymentService = $paymentService->Register($payment, $orderAmount, route('payment-paid'), route('payment-error'));
//
//            if ($paymentService->status) {
//                return ResultGenerate::Success($flashMessage, [
//                    'paymentLink' => $paymentService->paymentLink,
//                ]);
//            }
//
//            return ResultGenerate::Error($flashMessage);
//        }

        return ResultGenerate::Success($flashMessage);
    }

    private static function CleanClientInformation($clientInformation)
    {
        $clientInformation->clientName = str_replace(["'", "\n", "\r", "\r\n"], '', $clientInformation->clientName);
        $clientInformation->clientAddressDelivery = str_replace(["'", "\n", "\r", "\r\n"], '', $clientInformation->clientAddressDelivery);
        $clientInformation->clientComment = str_replace(["'", "\n", "\r", "\r\n"], '', $clientInformation->clientComment);
        return $clientInformation;
    }

    private function SendTelegram($orderFullInformation, $editOrder)
    {
        $allProductsInOrder = $orderFullInformation->products;
        $clientInformation = $orderFullInformation->clientInformation;

        $products = '';
        foreach ($allProductsInOrder as $key => $product) {
            $products .= $key + 1 . '. ' . $product->title . ' - ' . $product->amount . PHP_EOL;
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
        $message .= $products . PHP_EOL;
        $message .= '<i>Итого:</i> ' . $orderFullInformation->totalOrderAmount . ' ₽' . PHP_EOL;
        $message .= '<i>Итого со скидками:</i> ' . $orderFullInformation->orderAmount . ' ₽' . PHP_EOL;
        $message .= '<i>Заказ в системе:</i> ' . route('manager-arm-order-page', $orderFullInformation->orderId) . PHP_EOL;

        $telegram = new Telegram();
        if ($editOrder) {
            return $telegram->editMessageText($message, env('TELEGRAM_BOT_ORDERS_CHAT'), $orderFullInformation->order->order_telegram_message_id);
        } else {
            return $telegram->sendMessage($message, env('TELEGRAM_BOT_ORDERS_CHAT'));
        }
    }

    public static function SearchByPhone(string $phone)
    {
        $user = User::where('phone', 'like', '%' . $phone . '%')->first();
        $orders = [];
        if ($user) {
            $orders = $user->Orders;
        }
        return $orders;
    }

    public static function ChangeStatus(Orders $order, $statusId, $userId = 0)
    {
        OrdersStatusLogs::create([
            'order_id' => $order->id,
            'old_status_id' => $order->status_id,
            'new_status_id' => $statusId,
            'user_id' => $userId === 0 ? auth()->user()->id : $userId,
        ]);

        $oldStatusId = $order->status_id;

        $order->status_id = $statusId;

        if ($order->status_id === $oldStatusId && $statusId === Orders::STATUS_TEXT['courier'] && $order->courier_id === $userId) {
            $order->courier_id = 0;
        }

        $order->save();

        event(new Pusher($order->id, $oldStatusId, $statusId));
        return true;
    }

    public static function ChangePaymentType(Orders $order, array $paymentType)
    {
        $clientRawData = json_decode($order->client_raw_data);
        $clientRawData->typePayment = $paymentType;
        $order->client_raw_data = json_encode($clientRawData);
        return $order->save();
    }

    public static function OrderProductChangeStatus(ProductsModificationsInOrders $product, $status_id)
    {
        if ($product->status_id !== $status_id) {
            OrdersProductsStatusLogs::create([
                'order_product_id' => $product->id,
                'old_status_id' => $product->status_id,
                'new_status_id' => $status_id,
                'user_id' => auth()->user()->id,
            ]);
            $product->status_id = $status_id;
        }
        return $product->save();
    }

    public function OrderByIdForKitchenInterface()
    {
        $orderId = \request()->orderId;
        $order = Orders::find($orderId);
        $order->sendToKitchen = OrdersStatusLogs::where('order_id', $order->id)->where('new_status_id', Orders::STATUS_TEXT['kitchen'])->first('created_at')->created_at->format('H:m');

        $productsInOrder = [];
        foreach (json_decode($order->products_raw_data) as $product) {
            $productsInOrder[] = [
                'categoryId' => (int)$product->data->product->categoryId,
                'title' => $product->data->product->categoryTitle . ' ' . $product->data->product->title . ' ' . ($product->data->modification->title !== 'Соло-продукт' ? $product->data->modification->title . ' ' . $product->data->modification->value : ''),
                'amount' => $product->amount,
            ];
        }

        $order->products = $productsInOrder;
        unset($order->client_raw_data);
        unset($order->all_information_raw_data);
        unset($order->created_at);
        unset($order->order_amount);
        unset($order->status_id);
        unset($order->telegram_message_id);
        unset($order->total_order_amount);
        unset($order->updated_at);
        unset($order->user_id);
        unset($order->courier_id);
        unset($order->products_raw_data);
        return $order;
    }
}
