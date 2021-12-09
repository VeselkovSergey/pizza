@extends('app')

@section('content')

    <style>
        .order-change-status:hover, .order-edit-button:hover, .back-to-orders:hover {
            transform: scale(1.1);
        }
    </style>

    <div>
        <button class="mb-10 cp back-to-orders">
            <a class="clear-a" href="{{route('manager-arm-orders-page')}}">Назад к заказам</a>
        </button>
        @if($order->status_id === \App\Models\Orders::STATUS_TEXT['managerProcesses'])
        <button class="hide mb-10 cp order-edit-button">Редактировать заказ</button>
        @endif

        <div class="mb-10">
            @switch($order->status_id)
                @case(\App\Models\Orders::STATUS_TEXT['newOrder'])
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-2" data-url="{{route('manager-arm-change-status-order-to-manager-processes-page')}}">Взять в работу</button>
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-9" data-url="{{route('manager-arm-change-status-order-to-canceled-page')}}">Отказ</button>
                    @break
                @case(\App\Models\Orders::STATUS_TEXT['managerProcesses'])
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-1" data-url="{{route('manager-arm-change-status-order-to-new-order-page')}}">Вернуть в статус: Новый</button>
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-3" data-url="{{route('manager-arm-transfer-order-to-kitchen-page')}}">Передать на кухню</button>
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-9" data-url="{{route('manager-arm-change-status-order-to-canceled-page')}}">Отказ</button>
                    @break
                @case(\App\Models\Orders::STATUS_TEXT['cooked'])
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-6" data-url="{{route('manager-arm-transfer-order-to-delivery-page')}}">Передать в доставку</button>
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-9" data-url="{{route('manager-arm-change-status-order-to-canceled-page')}}">Отказ</button>
                    @break
                @case(\App\Models\Orders::STATUS_TEXT['courier'])
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-7" data-url="{{route('manager-arm-change-status-order-to-delivered')}}">Доставлен</button>
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-9" data-url="{{route('manager-arm-change-status-order-to-canceled-page')}}">Отказ</button>
                    @break
                @case(\App\Models\Orders::STATUS_TEXT['delivered'])
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-8" data-url="{{route('manager-arm-change-status-order-to-completed-page')}}">Выполнен</button>
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-9" data-url="{{route('manager-arm-change-status-order-to-canceled-page')}}">Отказ</button>
                    @break
            @endswitch
        </div>

        <div class="mb-10 p-5 order-status-{{$order->status_id}}">Заказ {{$order->created_at}} {{\App\Models\Orders::STATUS[$order->status_id]}}</div>

        <div>
            <div class="toggle-button cp" data-toogle="status-log-container">Изменения статуса:</div>
            <div class="ml-10 status-log-container">
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
                    <div>Сумма к оплате: {{$rawData->orderSum}} ₽</div>
                    <div>Оплата: {{$clientInfo->typePayment[0] === true ? 'Карта' : 'Наличные'}}</div>
                    <div>Адрес доставки: {{$clientInfo->clientAddressDelivery}}</div>
                    <div>Комментарий: {{$clientInfo->clientComment}}</div>
                </div>
            </div>
            <div>
                @php($productsAndModificationsInOrderForOrderEdit = [])
                @foreach($productsModificationsInOrder as $productModificationInOrder)
                    @php($productsAndModificationsInOrderForOrderEdit[] = (object)['productId' => $productModificationInOrder->ProductModifications->Product->id, 'modificationId' => $productModificationInOrder->product_modification_id, 'amount' => $productModificationInOrder->product_modification_amount, 'modificationTypeId' => $productModificationInOrder->ProductModifications->Modification->type_id])
                    <div class="p-5 mb-10 product-in-order-status-{{$productModificationInOrder->status_id}}">
                        <div>{{\App\Models\ProductsModificationsInOrders::STATUS[$productModificationInOrder->status_id]}}</div>
                        <div>{{$productModificationInOrder->ProductModifications->Product->title . ' ' . $productModificationInOrder->ProductModifications->Modification->title . ' ' . $productModificationInOrder->ProductModifications->Modification->value}}</div>
                        <div>{{'Цена: ' . $productModificationInOrder->ProductModifications->selling_price . ' ₽'}}</div>
                        <div>Кол-во: {{$productModificationInOrder->product_modification_amount}}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@stop

@section('js')

    <script>

        let allProducts = {!! json_encode($allProducts, JSON_UNESCAPED_UNICODE) !!};
        let productsAndModificationsInOrderForOrderEdit = {!! json_encode($productsAndModificationsInOrderForOrderEdit, JSON_UNESCAPED_UNICODE) !!};

        let buttonsOrderChangeStatus = document.body.querySelectorAll('.order-change-status');
        buttonsOrderChangeStatus.forEach((button) => {
            button.addEventListener('click', () => {
                let url = button.dataset.url;
                if (url === "{{route('manager-arm-transfer-order-to-delivery-page')}}") {
                    CreateModalWindowForCourierSelection(url);
                } else {
                    Ajax(url, 'post', {orderId: {{$order->id}}}).then(() => {
                        location.reload();
                    });
                }
            });
        });

        function CreateModalWindowForCourierSelection(url) {

            let container =
                '<div class="flex-column-center selector-courier">' +
                '<div class="mb-15">Выберите курьера</div>' +
                @foreach($couriers as $courier)
                    '<label class="p-5 cp"><input type="radio" name="courier" @if($loop->first) checked @endif value="{{$courier->id}}">{{$courier->name}}</label>' +
                @endforeach
                '<button class="mt-15 select-courier-button">Подтвердить</button>' +
                '</div>';

            let modalWindow = ModalWindow(container);

            let button = modalWindow.querySelector('.select-courier-button');
            button.addEventListener('click', () => {
                let courierId = modalWindow.querySelector('.selector-courier input:checked').value;

                Ajax(url, 'post', {orderId: {{$order->id}}, courierId: courierId}).then(() => {
                    location.reload();
                });

            });
        }

        ToggleShow();

        let orderStatuses = JSON.parse(localStorage.getItem('orderStatuses'));
        const orderId = {{$order->id}};
        const orderStatus = {{$order->status_id}};

        if (orderStatuses !== null) {
            Object.keys(orderStatuses).forEach((key) => {
                let order = orderStatuses[key];
                if (order.orderId === orderId) {
                    orderStatuses[key].oldStatus = orderStatus;
                }
            });
        }

        localStorage.setItem('orderStatuses', JSON.stringify(orderStatuses));

        let orderEditButton = document.body.querySelector('.order-edit-button');
        orderEditButton.addEventListener('click', () => {

            DeleteAllProductsInBasket();

            Object.keys(productsAndModificationsInOrderForOrderEdit).forEach((key) => {

                let modification = {
                    product: allProducts['product-'+productsAndModificationsInOrderForOrderEdit[key].productId],
                    modification: allProducts['product-'+productsAndModificationsInOrderForOrderEdit[key].productId]['modifications']['modification-type-'+productsAndModificationsInOrderForOrderEdit[key].modificationTypeId]['modification-'+productsAndModificationsInOrderForOrderEdit[key].modificationId],
                }

                for (let i = 0; i < productsAndModificationsInOrderForOrderEdit[key].amount; i++) {
                    AddProductInBasket(modification);
                }

            });

            let lastClientName = '{{$clientInfo->clientName}}';
            let lastClientPhone = '{{$clientInfo->clientPhone}}';
            let lastClientAddressDelivery = '{{$clientInfo->clientAddressDelivery}}';
            let lastClientComment = '{{$clientInfo->clientComment}}';
            let lastTypePayment = '{{$clientInfo->typePayment[0] === true ? 'card' : 'cash'}}';

            localStorage.setItem('lastClientName', lastClientName);
            localStorage.setItem('lastClientPhone', lastClientPhone);
            localStorage.setItem('lastClientAddressDelivery', lastClientAddressDelivery);
            localStorage.setItem('lastClientComment', lastClientComment);
            localStorage.setItem('lastTypePayment', lastTypePayment);

            BasketWindow(true);

        });


    </script>

@stop
