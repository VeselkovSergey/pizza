<?php


namespace App\Http\Controllers\Orders;

use App\Helpers\ArrayHelper;
use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Products\ProductsController;
use App\Models\Orders;
use App\Models\OrdersStatusLogs;
use App\Models\ProductModifications;
use App\Models\Products;
use App\Models\ProductsModificationsInOrders;
use App\Models\User;
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

        $newOrder = Orders::create([
            'user_id' => auth()->user()->id,
            'status_id' => Orders::STATUS_TEXT['clientCreateOrder'],
            'client_raw_data' => json_encode($clientInformation),
            'products_raw_data' => json_encode($basket),
            'all_information_raw_data' => json_encode($request->all()),
        ]);
        $orderId = $newOrder->id;
        self::ChangeStatus($newOrder, Orders::STATUS_TEXT['newOrder']);

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

        $productsModificationsInNewOrder = [];
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

            $dataModificationForDB = [
                'order_id' => $orderId,
                'status_id' => ProductsModificationsInOrders::STATUS_TEXT['new'],
                'product_modification_id' => $productsModification->id,
                'product_modification_amount' => $amountProductModificationInOrder[$productsModification->id],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $productsModificationsInNewOrder[] = $dataModificationForDB;
        }

        $newProductsModificationsInNewOrder = ProductsModificationsInOrders::query()->insert($productsModificationsInNewOrder);

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
        $message .= $products . PHP_EOL;
        $message .= '<i>Итого:</i> ' . $orderFullInformation->orderSum . ' ₽' . PHP_EOL;

        $telegram = new Telegram();
        $telegram->sendMessage($message, '-1001538892405');
//        $telegram->sendMessage($message, '267236435');
    }

    public static function AllOrders()
    {
        return Orders::all();
    }

    public static function Order(int $orderId)
    {
        return Orders::find($orderId);
    }

    public static function Statuses(Orders $order)
    {
        return $order->Statuses;
    }

    public static function OrderProductsModifications(Orders $order)
    {
        return $order->ProductsModifications;
    }

    public static function SearchByPhone(string $phone)
    {
        $user = User::query()->where('phone', $phone)->first();
        $orders = [];
        if ($user) {
            $orders = $user->Orders;
        }
        return $orders;
    }

    public static function ChangeStatus(Orders $order, $status_id)
    {
        if ($order->status_id !== $status_id) {
            OrdersStatusLogs::query()->create([
                'order_id' => $order->id,
                'old_status_id' => $order->status_id,
                'new_status_id' => $status_id,
                'user_id' => 1,//auth()->user()->id, #toDo включить когда сделаю авторизацию для сотрудников
            ]);
            $order->status_id = $status_id;
        }
        return $order->save();
    }
}
