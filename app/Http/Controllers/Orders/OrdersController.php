<?php


namespace App\Http\Controllers\Orders;

use App\Helpers\ArrayHelper;
use App\Helpers\ResultGenerate;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Payments\PaymentsController;
use App\Http\Controllers\Products\ProductsController;
use App\Models\Orders;
use App\Models\OrdersProductsStatusLogs;
use App\Models\OrdersStatusLogs;
use App\Models\Payments;
use App\Models\ProductModifications;
use App\Models\Products;
use App\Models\ProductsModificationsInOrders;
use App\Models\User;
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
        $paymentType = $clientInformation->typePayment[0];

        $clientInformation->clientPhone = preg_replace("/[^0-9]/", '', $clientInformation->clientPhone);

        $orderId = $request->orderId !== 'null' ? (int)$request->orderId : false;

        $user = User::where('phone', $clientInformation->clientPhone)->first();

        if (!$user) {
            $user = AuthController::FastRegistrationUserByPhone($clientInformation->clientPhone);
        }

        if ($user->IsAdmin()) {
            $request->merge(['orderSum' => (int)($request->orderSum / 2)]);
        }

        $orderSumFront = $request->orderSum;

        if (!empty($orderId)) {
            $order = Orders::find($orderId);
            if (empty($order)) {
                return ResultGenerate::Error('Произошла ошибка! Создайте новый заказ. Этот переведите в статус ОТКАЗ');
            }
            $order->client_raw_data = json_encode($clientInformation);
            $order->products_raw_data = json_encode($basket);
            $order->all_information_raw_data = json_encode($request->all());
            $order->save();
            ProductsModificationsInOrders::where('order_id', $orderId)->delete();
            $flashMessage = 'Заказ обновлен. Не забудь обновить страницу с заказом.';
        } else {
            $newOrder = Orders::create([
                'user_id' => $user->id,
                'status_id' => Orders::STATUS_TEXT['clientCreateOrder'],
                'client_raw_data' => json_encode($clientInformation),
                'products_raw_data' => json_encode($basket),
                'all_information_raw_data' => json_encode($request->all()),
            ]);
            $order = $newOrder;
            $orderId = $order->id;
            self::ChangeStatus($order, Orders::STATUS_TEXT['newOrder']);
            $flashMessage = 'Заказ принят. Мы скоро свяжемся с вами.';
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
        $orderSum = 0;

        foreach ($productsModifications as $productsModification) {

            $dataModification = (object)[
                'id' => $productsModification->id,
                'title' => $productsModification->Product->title . ' - ' . $productsModification->Modification->title . ' - ' . $productsModification->Modification->value . ' - ' . $productsModification->selling_price . ' ₽',
                'amount' => $amountProductModificationInOrder[$productsModification->id],
            ];
            $orderSum = $orderSum + ($productsModification->selling_price * $amountProductModificationInOrder[$productsModification->id]);
            $orderFullInformationAboutOrderedProduct[] = $dataModification;

            $newProductModificationInNewOrder = ProductsModificationsInOrders::create([
                'order_id' => $orderId,
                'status_id' => ProductsModificationsInOrders::STATUS_TEXT['create'],
                'product_modification_id' => $productsModification->id,
                'product_modification_amount' => $amountProductModificationInOrder[$productsModification->id]
            ]);
            self::OrderProductChangeStatus($newProductModificationInNewOrder, ProductsModificationsInOrders::STATUS_TEXT['new']);
        }

        $orderFullInformation = (object)[
            'products' => $orderFullInformationAboutOrderedProduct,
            'orderSum' => $orderSum,
            'orderSumFront' => $orderSumFront,
            'clientInformation' => $clientInformation,
            'orderId' => $orderId,
        ];

        $this->SendTelegram($orderFullInformation);

        AuthController::UpdateUserName($user, $clientInformation->clientName);

//        $paymentTypeString = $paymentType ? 'bank' : 'cash';
//
//        $payment = PaymentsController::CreatePayment($order, $orderSumFront, $paymentTypeString);
//
//        if ($paymentType) {
//            $paymentService = new SberBank();
//            $paymentService = $paymentService->Register($payment, $orderSumFront, route('payment-paid'), route('payment-error'));
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

    private function SendTelegram($orderFullInformation)
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
        $message .= '<i>Итого:</i> ' . $orderFullInformation->orderSum . ' ₽' . PHP_EOL;
        $message .= '<i>Итого со скидками:</i> ' . $orderFullInformation->orderSumFront . ' ₽' . PHP_EOL;
        $message .= '<i>Заказ в системе:</i> ' . route('manager-arm-order-page', $orderFullInformation->orderId) . PHP_EOL;

        $telegram = new Telegram();
        $telegram->sendMessage($message, '-1001538892405');
//        $telegram->sendMessage($message, '267236435');
    }

    public static function AllOrders()
    {
        return Orders::query()->orderBy('id', 'desc')->get();
    }

    public static function OrdersByDate($requiredDate, $allOrders = false)
    {
        $requiredDate = strtotime($requiredDate);
        $startDate = date('Y-m-d 00:00:00', $requiredDate);
        $endDate = date('Y-m-d 23:59:59', $requiredDate);
        $orders = new Orders();
        $orders = $orders->where('created_at', '>=', $startDate);
        $orders = $orders->where('created_at', '<=', $endDate);
        $orders = $orders->where('created_at', '<=', $endDate);
        if (!$allOrders) {
            $orders = $orders->whereNotIn('status_id', [Orders::STATUS_TEXT['completed'], Orders::STATUS_TEXT['cancelled']]);
        }
        $orders = $orders->orderBy('id', 'desc');
        return $orders->get();
    }

    public static function KitchenOrdersOnly()
    {
        return Orders::where('status_id', Orders::STATUS_TEXT['kitchen'])->get();
    }

    /**
     * @param int $orderId
     * @return Orders
     */
    public static function Order(int $orderId)
    {
        return Orders::find($orderId);
    }

    public static function OrderStatuses(Orders $order)
    {
        return $order->Statuses;
    }

    public static function OrderProductsModifications(Orders $order)
    {
        return $order->ProductsModifications;
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

    public static function ChangeStatus(Orders $order, $status_id)
    {
        if ($order->status_id !== $status_id) {
            OrdersStatusLogs::create([
                'order_id' => $order->id,
                'old_status_id' => $order->status_id,
                'new_status_id' => $status_id,
                'user_id' => auth()->user()->id,
            ]);
            $order->status_id = $status_id;
        }
        return $order->save();
    }

    public static function OrderProduct($productId)
    {
        return ProductsModificationsInOrders::find($productId);
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
}
