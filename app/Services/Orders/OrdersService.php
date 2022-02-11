<?php

namespace App\Services\Orders;

use App\Models\Orders;
use App\Models\ProductModifications;
use App\Models\ProductModificationsIngredients;
use App\Models\ProductsModificationsInOrders;
use Illuminate\Support\Facades\Cache;

class OrdersService
{
    protected Orders $order;
    protected $orderCreatedAt;
    protected $orderCost = 0;
    protected $orderProductAmount = 0;
    protected $orderIsLongTime;
    protected $orderTimeCookedToDelivered;
    protected $orderIsLongTimeCookedToDelivered;
    protected $orderTime;
    protected $ordersStatistics;

    public function GetOrderByPeriod($period)
    {
        $startDate = date('Y-m-d 00:00:00', strtotime($period[0]));
        $endDate = date('Y-m-d 23:59:59', strtotime($period[1]));

        $ordersModels = Orders::select('id', 'client_raw_data', 'courier_id', 'user_id', 'order_amount', 'total_order_amount', 'created_at', 'status_id')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->get();

        $this->ordersStatistics = new \stdClass();
        
        $this->ordersStatistics->ordersAmount = 0;
        $this->ordersStatistics->ordersAmountWithoutNotDelivery = 0;
        $this->ordersStatistics->ordersAmountCash = 0;
        $this->ordersStatistics->ordersAmountBank = 0;


        $this->ordersStatistics->ordersNotDelivery = 0;

        $this->ordersStatistics->ordersCostAmount = 0;

        $this->ordersStatistics->ordersCreatorWeb = 0;
        $this->ordersStatistics->ordersCreatorManager = 0;
        $this->ordersStatistics->ordersCreatorAdmin = 0;

        $this->ordersStatistics->amountOrdersCancelled = 0;

        $this->ordersStatistics->ordersByCouriers = [];
        $this->ordersStatistics->ordersAmountInDays = [];
        $this->ordersStatistics->ordersNumberInDays = [];
        $this->ordersStatistics->ordersNumberInHour = [];
        
        $ordersWithInfo = [];
        foreach ($ordersModels as $orderModel) {

            $order = Cache::get('order-' . $orderModel->id);

            if (empty($order)) {
                $order = $this->OrderInfo($orderModel);
                Cache::put('order-' . $orderModel->id, $order);
            }

            $this->OrderStatistics($order);

            

            $ordersWithInfo[] = $order;
        }

        return ['orders' => $ordersWithInfo, 'ordersStatistics' => $this->ordersStatistics];
    }

    public function OrderInfo(Orders $orderInput)
    {
        $this->order = $orderInput;
        $this->orderCreatedAt = $this->order->created_at;
        $this->orderCost = 0;
        $this->orderProductAmount = 0;

        $orderStd = new \stdClass();

        $orderStd->id = $this->order->id;

        $clientInfo = json_decode($this->order->client_raw_data);
        $clientInfo->typePaymentId = ($clientInfo->typePayment[0] === true ? 0 : 1);
        $clientInfo->typePaymentText = ($clientInfo->typePayment[0] === true ? 'Карта' : 'Наличные');
        $clientInfo->ordersCount = $this->order->User->Orders->count();
        $clientInfo->userId = $this->order->User->id;
        $orderStd->clientInfo = $clientInfo;
        $orderStd->clientInfo = $clientInfo;

        $courier = $this->order->Courier;
        $orderStd->courierId = $courier ? $courier->id : '-';
        $orderStd->courierName = $courier ? $courier->name : '-';
        $orderStd->courierPhone = $courier ? $courier->phone : '-';

        $creator = $this->order->Creator()->User;
        $orderStd->creatorId = $creator->id;
        $orderStd->creatorName = $creator->name;
        $orderStd->creatorPhone = $creator->phone;
        $orderStd->creatorTypeId = $creator->UserIsAdmin() || $this->order->User->UserIsAdmin() ? 999 : ($creator->UserIsManager() ? 777 : 1);
        $orderStd->creatorType = $creator->UserIsAdmin() ? 'Собственник' : ($creator->UserIsManager() ? 'Менеджер' : 'Сайт');

        $orderStd->amount = $this->order->order_amount;
        $orderStd->totalAmount = $this->order->total_order_amount;

        $orderStd->createdAt = $this->orderCreatedAt;

        $orderStd->statusId = $this->order->status_id;
        $orderStd->statusText = Orders::STATUS[$this->order->status_id];

        $orderStd->products = $this->OrderProductsInfo();
        $orderStd->cost = $this->orderCost;

        $orderStd->statuses = $this->OrderStatuses();
        $orderStd->isLongTime = $this->orderIsLongTime;
        $orderStd->orderTime = $this->orderTime;

        $orderStd->timeCookedToDelivered = $this->orderTimeCookedToDelivered;
        $orderStd->isLongTimeCookedToDelivered = $this->orderIsLongTimeCookedToDelivered;

        $orderStd->isCompleted = $this->order->IsCompleted();
        $orderStd->isCancelled = !$orderStd->isCompleted && $this->order->IsCancelled();

        $orderStd->productsAmount = $this->orderProductAmount;

        return $orderStd;
    }

    public function OrderProductsInfo()
    {
        $products = [];

        /** @var ProductsModificationsInOrders $productModificationInOrder */
        foreach ($this->order->ProductsModifications as $productModificationInOrder) {

            $productStd = new \stdClass();

            $productStd->statusId = $productModificationInOrder->status_id;
            $productStd->statusText = ProductsModificationsInOrders::STATUS[$productModificationInOrder->status_id];

            $productStd->amount = $productModificationInOrder->product_modification_amount;

            $productModification = $productModificationInOrder->ProductModifications;
            $productStd->sellingPrice = $productModification->selling_price;

            $product = $productModification->Product;
            $productTitle = $product->title;

            $category = $product->Category;
            $categoryTitle = $category->title;

            $modification = $productModification->Modification;
            $modificationTitle = $modification->title;
            $modificationValue = $modification->value;

            $productStd->title = $categoryTitle . ' ' . $productTitle . ' ' . $modificationTitle . ' ' . $modificationValue;

            $modificationIngredients = $this->ModificationIngredients($productModification, $this->orderCreatedAt);

            $productStd->ingredients = $modificationIngredients->ingredients;
            $productStd->cost = $modificationIngredients->productModificationCost;

            $this->orderProductAmount += $productStd->amount;
            $this->orderCost += $productStd->cost * $productStd->amount;

            $products[] = $productStd;
        }

        return $products;
    }

    public static function ModificationIngredients(ProductModifications $productModification, $date)
    {
        $productModificationIngredients = $productModification->Ingredients;

        $ingredients = [];
        $productModificationCost = 0;
        $productModificationWeight = 0;

        /** @var ProductModificationsIngredients $productModificationIngredient */
        foreach ($productModificationIngredients as $productModificationIngredient) {
            $ingredientStd = new \stdClass();

            $ingredientStd->amount = $productModificationIngredient->ingredient_amount;

            $ingredient = $productModificationIngredient->Ingredient;
            $ingredientStd->id = $ingredient->id;
            $ingredientStd->title = $ingredient->title;

            $ingredientStd->visible = (bool)$productModificationIngredient->visible;

            $ingredientStd->ubitPrice = (float)$ingredient->PriceByDate($date);
            $ingredientStd->price = $ingredientStd->ubitPrice * $ingredientStd->amount;

            $ingredients[] = $ingredientStd;

            $productModificationCost += $ingredientStd->price;
            $productModificationWeight += $ingredientStd->visible ? $ingredientStd->amount : 0;
        }

        $obj = new \stdClass();
        $obj->ingredients = $ingredients;
        $obj->productModificationCost = $productModificationCost;
        $obj->productModificationWeight = $productModificationWeight;
        return $obj;
    }

    public function OrderStatuses()
    {
        $statuses = (object)[];

        $statuses->timeManagerProcesses = $this->order->TimeManagerProcesses();
        $statuses->timeTransferOnKitchen = $this->order->TimeTransferOnKitchen();
        $statuses->timeCooked = $this->order->TimeCooked();
        $statuses->timeCourier = $this->order->TimeCourier();
        $statuses->timeDelivered = $this->order->TimeDelivered();
        $statuses->timeCompleted = $this->order->TimeCompleted();

        $statuses->lastStatusTime = $this->order->LatestStatus->updated_at;

        $this->orderIsLongTime = date_diff($this->orderCreatedAt, $this->order->LatestStatus->updated_at)->format('%H') !== '00';
        $this->orderTime = Orders::TimeBetweenStatuses($this->order->id, Orders::STATUS_TEXT['newOrder'], Orders::STATUS_TEXT['completed']);
        $this->orderTimeCookedToDelivered = Orders::TimeBetweenStatuses($this->order->id, Orders::STATUS_TEXT['kitchen'], Orders::STATUS_TEXT['delivered']);
        $this->orderIsLongTimeCookedToDelivered = date('H', strtotime($this->orderTimeCookedToDelivered)) !== '00';

        return $statuses;
    }
    
    public function OrderStatistics($order)
    {
        if ($order->isCompleted) {

            $this->ordersStatistics->ordersAmount += $order->amount;

            if ($order->courierId !== '-') {
                $this->ordersStatistics->ordersNotDelivery++;
                $this->ordersStatistics->ordersAmountWithoutNotDelivery += $order->amount;
            }

            $this->ordersStatistics->ordersCostAmount += $order->cost;
            $order->clientInfo->typePaymentId === 0 ? $this->ordersStatistics->ordersAmountBank += $order->amount : $this->ordersStatistics->ordersAmountCash += $order->amount;
            $order->creatorTypeId === 1 ? $this->ordersStatistics->ordersCreatorWeb++ : ($order->creatorTypeId === 777 ? $this->ordersStatistics->ordersCreatorManager++ : $this->ordersStatistics->ordersCreatorAdmin++);
            empty($this->ordersStatistics->ordersNumberInHour[(int)$order->createdAt->format('H')]) ? $this->ordersStatistics->ordersNumberInHour[(int)$order->createdAt->format('H')] = 1 : $this->ordersStatistics->ordersNumberInHour[(int)$order->createdAt->format('H')] += 1;

            if (empty($this->ordersStatistics->ordersAmountInDays[$order->createdAt->format('Y-m-d')])) {
                $this->ordersStatistics->ordersAmountInDays[$order->createdAt->format('Y-m-d')]['ordersNumber'] = 0;
                $this->ordersStatistics->ordersAmountInDays[$order->createdAt->format('Y-m-d')]['ordersAmount'] = 0;

                $this->ordersStatistics->ordersAmountInDays[$order->createdAt->format('Y-m-d')]['ordersNumberCash'] = 0;
                $this->ordersStatistics->ordersAmountInDays[$order->createdAt->format('Y-m-d')]['ordersNumberBank'] = 0;

                $this->ordersStatistics->ordersAmountInDays[$order->createdAt->format('Y-m-d')]['ordersAmountCash'] = 0;
                $this->ordersStatistics->ordersAmountInDays[$order->createdAt->format('Y-m-d')]['ordersAmountBank'] = 0;
            }

            $this->ordersStatistics->ordersAmountInDays[$order->createdAt->format('Y-m-d')]['ordersNumber']++;
            $this->ordersStatistics->ordersAmountInDays[$order->createdAt->format('Y-m-d')]['ordersAmount'] += $order->amount;

            if ($order->clientInfo->typePaymentId === 0) {
                $this->ordersStatistics->ordersAmountInDays[$order->createdAt->format('Y-m-d')]['ordersNumberBank']++;
                $this->ordersStatistics->ordersAmountInDays[$order->createdAt->format('Y-m-d')]['ordersAmountBank'] += $order->amount;
            } else {
                $this->ordersStatistics->ordersAmountInDays[$order->createdAt->format('Y-m-d')]['ordersNumberCash']++;
                $this->ordersStatistics->ordersAmountInDays[$order->createdAt->format('Y-m-d')]['ordersAmountCash'] += $order->amount;
            }

        } else {
            $this->ordersStatistics->amountOrdersCancelled++;
        }

        if (isset($this->ordersStatistics->ordersByCouriers[$order->courierId])) {
            $this->ordersStatistics->ordersByCouriers[$order->courierId]['orderAmount']++;
        } elseif ($order->courierId !== '-') {
            $this->ordersStatistics->ordersByCouriers[$order->courierId] = ['name' => $order->courierName, 'phone' => $order->courierPhone, 'orderAmount' => 1];
        }
    }
}