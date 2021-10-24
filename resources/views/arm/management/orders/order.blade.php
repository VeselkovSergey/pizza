@extends('app')

@section('content')

    <style>
        .order-change-status:hover {
            transform: scale(1.1);
        }
    </style>

    <div>
        <a class="block mb-10" href="{{route('manager-arm-orders-page')}}">Назад к заказам</a>

        <div class="mb-10">
            <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-1" data-url="{{route('manager-arm-change-status-order-to-new-order-page')}}">Вернуть в статус: Новый</button>
            <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-2" data-url="{{route('manager-arm-change-status-order-to-manager-processes-page')}}">Взять в работу</button>
            <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-3" data-url="{{route('manager-arm-transfer-order-to-kitchen-page')}}">Передать на кухню</button>
            <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-6" data-url="{{route('manager-arm-transfer-order-to-delivery-page')}}">Передать в доставку</button>
            <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-7" data-url="{{route('manager-arm-change-status-order-to-completed-page')}}">Выполнен</button>
            <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-8" data-url="{{route('manager-arm-change-status-order-to-canceled-page')}}">Отказ</button>
        </div>

        <div class="mb-10 p-5 order-status-{{$order->status_id}}">Заказ {{$order->created_at}} {{\App\Models\Orders::STATUS[$order->status_id]}}</div>

        <div>
            <div>Изменения статуса:</div>
            <div class="ml-10">
                @foreach($orderStatuses as $orderStatus)
                    <div class="p-5 m-5">{{$orderStatus->created_at}}
                        <span class="p-5 order-status-{{$orderStatus->old_status_id}}">{{\App\Models\Orders::STATUS[$orderStatus->old_status_id]}}</span>
                        >
                        <span class="p-5 order-status-{{$orderStatus->new_status_id}}">{{\App\Models\Orders::STATUS[$orderStatus->new_status_id]}}</span>
                        ({{$orderStatus->User->surname . ' ' . $orderStatus->User->name. ' ' . $orderStatus->User->patronymic}})
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <div class="mb-10">
                <div>Клиент:</div>
                <div class="ml-10">
                    <div>Имя: {{$clientInfo->clientName}}</div>
                    <div>Номер телефона: {{$clientInfo->clientPhone}}</div>
                    <div>Оплата: {{$clientInfo->typePayment[0] === true ? 'Карта' : 'Наличные'}}</div>
                    <div>Адрес доставки: {{$clientInfo->clientAddressDelivery}}</div>
                    <div>Комментарий: {{$clientInfo->clientComment}}</div>
                </div>
            </div>
            <div>
                @foreach($productsModificationsInOrder as $productModificationInOrder)
                    <div class="p-5 mb-10 product-in-order-status-{{$productModificationInOrder->status_id}}">
                        <div>{{\App\Models\ProductsModificationsInOrders::STATUS[$productModificationInOrder->status_id]}}</div>
                        <div>{{$productModificationInOrder->ProductModifications->Product->title . ' ' . $productModificationInOrder->ProductModifications->Modification->title . ' ' . $productModificationInOrder->ProductModifications->Modification->value . ' ' . $productModificationInOrder->ProductModifications->selling_price . ' ₽'}}</div>
                        <div>Кол-во: {{$productModificationInOrder->product_modification_amount}}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@stop

@section('js')

    <script>
        let buttonsOrderChangeStatus = document.body.querySelectorAll('.order-change-status');
        buttonsOrderChangeStatus.forEach((button) => {
            button.addEventListener('click', () => {
                let url = button.dataset.url;
                Ajax(url, 'post', {orderId: {{$order->id}}}).then(() => {
                    location.reload();
                });
            });
        });
    </script>

@stop