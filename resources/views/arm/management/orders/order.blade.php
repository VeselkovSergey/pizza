@extends('app')

@section('content')

    <div>
        <div class="flex-wrap">
            <a class="orange-button" href="{{route('manager-arm-orders-page')}}">Назад к заказам</a>
            @if($order->status_id === \App\Models\Orders::STATUS_TEXT['managerProcesses'] || auth()->user()->IsAdmin())
            <button class=" mb-5 orange-button order-edit-button">Редактировать заказ</button>
            @endif
            <button class="ml-a orange-button stack-invoices">Печать комплекта чеков</button>
            <a class="orange-button full-invoice" target="_blank" href="{{route('manager-arm-order-invoice-page', $order->id)}}">Печать полного чека</a>
            <a class="chef-invoice orange-button" target="_blank" href="{{route('manager-arm-order-invoice-chef-page', $order->id)}}">Печать чека для повара</a>
        </div>

        <h3>Если в заказе нет соусов/напитков - предлагаем</h3>
        <h3>Заказали пиццу - предлагаем пасту/салаты/стартеры</h3>
        <h3>Заказали не пиццу - предлагаем пиццу</h3>
        <h3>Начинать разговор можно с: "А знаете у на есть популярная %пицца/салат/паста% не хотите попробовать?"</h3>

        <div class="mb-10">
            @if(auth()->user()->IsAdmin() && $order->status_id !== \App\Models\Orders::STATUS_TEXT['courier'])
                <button class="change-courier-in-order clear-button py-5 px-25 mr-10 border-radius-5 cp red-button" data-url="{{route('manager-arm-change-courier-in-order')}}">Изменить курьера</button>
            @endif
            @switch($order->status_id)
                @case(\App\Models\Orders::STATUS_TEXT['newOrder'])
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-2" data-url="{{route('manager-arm-change-status-order-to-manager-processes-page')}}">Взять в работу</button>
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-9" data-url="{{route('manager-arm-change-status-order-to-canceled-page')}}">Отказ</button>
                    @break
                @case(\App\Models\Orders::STATUS_TEXT['managerProcesses'])
{{--                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-1" data-url="{{route('manager-arm-change-status-order-to-new-order-page')}}">Вернуть в статус: Новый</button>--}}
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-3" data-url="{{route('manager-arm-transfer-order-to-kitchen-page')}}">Передать на кухню</button>
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-9" data-url="{{route('manager-arm-change-status-order-to-canceled-page')}}">Отказ</button>
                    @break
                @case(\App\Models\Orders::STATUS_TEXT['kitchen'])
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-9" data-url="{{route('manager-arm-change-status-order-to-canceled-page')}}">Отказ</button>
                    @break
                @case(\App\Models\Orders::STATUS_TEXT['cooked'])
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-6" data-url="{{route('manager-arm-transfer-order-to-delivery-page')}}">Передать в доставку</button>
                    <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-9" data-url="{{route('manager-arm-change-status-order-to-canceled-page')}}">Отказ</button>
                    @break
                @case(\App\Models\Orders::STATUS_TEXT['courier'])
                    <button class="change-courier-in-order clear-button py-5 px-25 mr-10 border-radius-5 cp red-button" data-url="{{route('manager-arm-change-courier-in-order')}}">Изменить курьера</button>
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
                    <div class="p-5 m-5 flex-center-vertical flex-wrap" style="border: 1px solid grey;">
                        <div class="mr-5" style="min-width: 200px">{{$orderStatus->created_at}}</div>
                        <div class="mr-5 p-5 order-status-{{$orderStatus->old_status_id}}">{{\App\Models\Orders::STATUS[$orderStatus->old_status_id]}}</div>
                        <div class="mr-5">></div>
                        <div class="mr-5 p-5 order-status-{{$orderStatus->new_status_id}}">{{\App\Models\Orders::STATUS[$orderStatus->new_status_id]}}</div>
                        <div class="mr-5">({{$orderStatus->User->surname . ' ' . $orderStatus->User->name. ' ' . $orderStatus->User->patronymic}})</div>
                        @if($orderStatus->new_status_id === \App\Models\Orders::STATUS_TEXT['courier'])
                            {{$order->Courier ? $order->Courier->name . ' ' . $order->Courier->phone : 'Самовывоз'}}
                        @endif
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
                    <div>Сумма к оплате: {{$order->order_amount}} ₽</div>
                    <div>
                        <span>Оплата: {{$clientInfo->typePayment[0] === true ? 'Карта' : 'Наличные'}}</span>
                        <span class="cp edit-payment-type" data-payment-type="{{$clientInfo->typePayment[0] === true ? 'card' : 'cash'}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                              <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                            </svg>
                        </span>
                    </div>
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

    <div id="map" class="w-100" style="height: calc(100vh - 250px)"></div>

@stop

@section('js')

    <script>

        let allProducts = {!! json_encode($allProducts, JSON_UNESCAPED_UNICODE) !!};
        let productsAndModificationsInOrderForOrderEdit = {!! json_encode($productsAndModificationsInOrderForOrderEdit, JSON_UNESCAPED_UNICODE) !!};

        let buttonsOrderChangeStatus = document.body.querySelectorAll('.order-change-status, .change-courier-in-order');
        buttonsOrderChangeStatus.forEach((button) => {
            button.addEventListener('click', () => {
                localStorage.removeItem('orderId');
                let url = button.dataset.url;
                if (url === "{{route('manager-arm-transfer-order-to-delivery-page')}}" || url === "{{route('manager-arm-change-courier-in-order')}}") {
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
                    '<label class="p-5 cp"><input type="radio" name="courier" checked  value="0">Самовывоз</label>' +
                @foreach($couriers as $courier)
                    '<label class="p-5 cp"><input type="radio" name="courier" value="{{$courier->id}}">{{$courier->name}}</label>' +
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
        if (orderEditButton) {
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

                localStorage.setItem('lastClientName', '{{$clientInfo->clientName}}');
                localStorage.setItem('lastClientPhone', '{{$clientInfo->clientPhone}}');
                localStorage.setItem('lastClientAddressDelivery', '{{$clientInfo->clientAddressDelivery}}');
                localStorage.setItem('lastClientComment', '{{$clientInfo->clientComment}}');
                localStorage.setItem('lastTypePayment', '{{$clientInfo->typePayment[0] === true ? 'card' : 'cash'}}');
                localStorage.setItem('orderId', orderId);

                @if($promoCode)
                localStorage.setItem('promoCode', JSON.stringify({!! json_encode($promoCode->conditions, JSON_UNESCAPED_UNICODE) !!}));
                @endif

                localStorage.setItem('execFunction', 'BasketWindow()');

                window.open(
                    "{{route('catalog')}}",
                    '_blank'
                );

            });
        }

        let stackInvoicesButton = document.body.querySelector('.stack-invoices');
        stackInvoicesButton.addEventListener('click', () => {
            window.open(
                "{{route('manager-arm-order-invoice-chef-page', $order->id)}}",
                '_blank'
            );
            window.open(
                "{{route('manager-arm-order-invoice-page', $order->id)}}",
                '_blank'
            );
            window.open(
                "{{route('manager-arm-order-invoice-page', $order->id)}}",
                '_blank'
            );
        });

        let editPaymentTypeButton = document.body.querySelector('.edit-payment-type');
        editPaymentTypeButton.addEventListener('click', () => {
            let paymentType = editPaymentTypeButton.dataset.paymentType;
            EditPaymentTypeWindow({{$order->id}}, paymentType);
        });

        function EditPaymentTypeWindow(orderId, oldPaymentType) {

            let container =
                '<div class="w-100 flex-wrap">' +
                    '<div class="w-100 mb-10">Изменить способ оплаты</div>' +
                    '<div class="flex w-100 payment-types">' +
                        '<div class="flex w-50">' +
                            '<label for="bank-payment">' +
                                '<input ' + ((oldPaymentType === 'card' || oldPaymentType === '' || oldPaymentType === undefined) ? 'checked' : '') + ' name="typePayment" value="card" type="radio" id="bank-payment" class="last-data hide">' +
                                '<span class="cp py-10 block text-center w-100">Карта</span>' +
                            '</label>' +
                        '</div>' +
                        '<div class="flex w-50">' +
                            '<label for="cash-payment">' +
                                '<input ' + (oldPaymentType === 'cash' ? 'checked' : '') + ' name="typePayment" type="radio" value="cash" id="cash-payment" class="last-data hide">' +
                                '<span class="cp py-10 block text-center w-100">Наличные</span>' +
                            '</label>' +
                        '</div>' +
                    '</div>' +
                    '<button class="ml-a mt-10 orange-button save-payment-type">Применить изменения</button>' +
                '</div>';

            let modalWindow = ModalWindow(container);

            let button = modalWindow.querySelector('.save-payment-type');
            button.addEventListener('click', () => {
                let data = GetDataFormContainer('payment-types');

                Ajax("{{route('manager-arm-order-change-payment-type')}}", 'post', {orderId: orderId, typePayment: JSON.stringify(data.typePayment)}).then(() => {
                    location.reload();
                });

            });
        }


    </script>

    <script src="//api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=00ad06f6-97e8-4b19-b114-304b35812efb" type="text/javascript"></script>

    <script>

        const APIKEY_YA = '00ad06f6-97e8-4b19-b114-304b35812efb';

        @if($clientInfo->clientPhone !== '70000000000')
        ymaps.ready(CalculateAndRender);
        @endif

        @if(empty($order->geo_yandex))
        function GeoCoder() {
            Ajax('https://geocode-maps.yandex.ru/1.x?apikey='+APIKEY_YA+'&format=json&results=1&geocode=Россия, Московская область, Дубна, ' + {{$clientInfo->clientAddressDelivery}}).then((res) => {
                let addressText = res.response.GeoObjectCollection.featureMember[0].GeoObject.metaDataProperty.GeocoderMetaData.Address.formatted;
                let position = res.response.GeoObjectCollection.featureMember[0].GeoObject.Point.pos;
                let coordinate = position.split(' ');

                let yandexGeo = {
                    addressText: addressText,
                    addressLat: coordinate[0],
                    addressLon: coordinate[1],
                }

                Ajax("{{route('order-update-geo-yandex')}}", "POST", {orderId: orderId, yandexGeo: JSON.stringify(yandexGeo)});
            });
        }
        GeoCoder();
        @endif

        function CalculateAndRender() {
            // Стоимость за километр.
            var DELIVERY_TARIFF = 20,
                // Минимальная стоимость.
                MINIMUM_COST = 10,
                myMap = new ymaps.Map('map', {
                    center: [56.734422, 37.162106],
                    zoom: 14,
                    controls: []
                });//,
            // Создадим панель маршрутизации.
            routePanelControl = new ymaps.control.RoutePanel({
                options: {
                    // Добавим заголовок панели.
                    showHeader: false,
                    title: 'Расчёт доставки'
                }
            });
            // Пользователь сможет построить только автомобильный маршрут.
            routePanelControl.routePanel.options.set({
                types: {auto: true}
            });

            // Если вы хотите задать неизменяемую точку "откуда", раскомментируйте код ниже.
            routePanelControl.routePanel.state.set({
                fromEnabled: false,
                from: 'Россия, Дубна, Московская область, улица Вернова, 9',
                toEnabled: true,
                to: "{{isset($order->geo_yandex) ? json_decode($order->geo_yandex)->addressText : 'Россия, Дубна, Московская область,' . $clientInfo->clientAddressDelivery}}",
            });

            let trafficControl = new ymaps.control.TrafficControl({ state: {
                    // Отображаются пробки "Сейчас".
                    providerKey: 'traffic#actual',
                    // Начинаем сразу показывать пробки на карте.
                    trafficShown: true
                }});

            myMap.controls.add(routePanelControl).add(trafficControl);

            // Получим ссылку на маршрут.
            routePanelControl.routePanel.getRouteAsync().then(function (route) {

                // Зададим максимально допустимое число маршрутов, возвращаемых мультимаршрутизатором.
                route.model.setParams({results: 1}, true);

                // Повесим обработчик на событие построения маршрута.
                route.model.events.add('requestsuccess', function () {

                    var activeRoute = route.getActiveRoute();
                    if (activeRoute) {
                        // Получим протяженность маршрута.
                        var length = route.getActiveRoute().properties.get("distance"),
                            // Вычислим стоимость доставки.
                            price = calculate(Math.round(length.value) / 1000),
                            // Создадим макет содержимого балуна маршрута.
                            balloonContentLayout = ymaps.templateLayoutFactory.createClass(
                                '<span>Расстояние: ' + length.text + '.</span><br/>' +
                                '<span style="font-weight: bold; font-style: italic">Стоимость доставки: ' + price + ' р.</span>');
                        // Зададим этот макет для содержимого балуна.
                        route.options.set('routeBalloonContentLayout', balloonContentLayout);
                        // Откроем балун.
                        activeRoute.balloon.open();
                    }
                });

            });
            // Функция, вычисляющая стоимость доставки.
            function calculate(routeLength) {
                console.log(routeLength)
                return Math.max(routeLength * DELIVERY_TARIFF, MINIMUM_COST).toFixed(2);
            }
        }
    </script>

@stop
