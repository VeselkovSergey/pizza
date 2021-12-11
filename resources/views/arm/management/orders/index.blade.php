@php
$orderStatuses = [];
@endphp

@extends('app')

@section('content')

    <style>
        .order:hover {
            transform: scale(1.01);
        }
        .hover-show {
            display: none;
        }
        .order:hover .hover-show {
            display: block;
        }
    </style>

    <div>
        <div>Заказы</div>
        <div>
            <label class="mb-5">
                Поиск по номеру телефона:
                <input class="search-orders-by-phone" type="text" placeholder="79991112233" maxlength="11">
            </label>
            <label>
                На какое число заказы
                <input class="required-date" type="date" value="{{$requiredDate}}">
                <button class="cp"><a class="clear-a" href="{{route('manager-arm-orders-page')}}">Заказы за сегодня</a></button>
                <button class="cp all-orders-required-date">показать выполненные/отказанные</button>
                <button class="cp order-without-cancelled-and-completed-required-date">скрыть выполненные/отказанные</button>
            </label>
        </div>
        <div class="orders-container">
            @foreach($orders as $order)
                @php($orderStatuses[] = (object)['orderId' => $order->id, 'oldStatus' => $order->status_id])
                @php($clientData = json_decode($order->client_raw_data))
                <a target="_blank" href="{{route('manager-arm-order-page', $order->id)}}" data-order-id="{{$order->id}}" data-order-status-id="{{$order->status_id}}" class="order flex-space-between clear-a border p-10 m-5 order-status-{{$order->status_id}}">
                    <div>
                        <div># {{$order->id}} {{\App\Models\Orders::STATUS[$order->status_id]}}</div>
                        <div class="order-info @if((\App\Models\Orders::STATUS_TEXT['cancelled'] === $order->status_id) || \App\Models\Orders::STATUS_TEXT['completed'] === $order->status_id) hover-show @endif">
                            <div>Имя: {{$clientData->clientName}}</div>
                            <div>Номер: {{$clientData->clientPhone}}</div>
                            <div>Адрес: {{$clientData->clientAddressDelivery}}</div>
                            <div>Комментарий: {{$clientData->clientComment}}</div>
                            <div>Сумма: {{json_decode($order->all_information_raw_data)->orderSum}}</div>
                            <div>Оплата: {{($clientData->typePayment[0] === true ? 'Карта' : 'Наличные')}}</div>
                            <div>{{$order->created_at}}</div>
                        </div>
                    </div>
                    <div class="order-alert-icon hide">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="found-orders-container hide"></div>
    </div>

@stop

@section('js')

    <script>

        localStorage.setItem('orderStatuses', JSON.stringify(@json((object)$orderStatuses)));

        let foundOrdersContainer = document.body.querySelector('.found-orders-container');
        let ordersContainer = document.body.querySelector('.orders-container');
        let buttonSearchOrdersByPhone = document.body.querySelector('.search-orders-by-phone');

        buttonSearchOrdersByPhone.addEventListener('input', (event) => {
            let field = event.target;
            let value = field.value;
            if (value.length > 9) {
                SearchOrdersByPhone(value);
            } else {
                foundOrdersContainer.hide();
                ordersContainer.show();
            }
        });

        function SearchOrdersByPhone(phone) {
            Ajax('{{route('manager-arm-order-search-bu-phone')}}', 'post', {phone: phone}).then((response) => {
                foundOrdersContainer.innerHTML = '';
                FoundOrdersGenerationHTML(response);
                ordersContainer.hide();
                foundOrdersContainer.show();
            });
        }

        function FoundOrdersGenerationHTML(foundOrders){
            let statuses = foundOrders.statuses;
            foundOrders = foundOrders.orders;
            Object.keys(foundOrders).forEach((key) => {
                let foundOrder = foundOrders[key];
                CreateElement('a', {
                    attr: {
                        href: '{{route('manager-arm-order-page')}}/' + foundOrder.id,
                        class: 'order block clear-a border p-10 m-5 order-status-' + foundOrder.status_id,
                        target: '_blank',
                    },
                    childs: [
                        CreateElement('div', {
                            content: statuses[foundOrder.status_id]
                        }),
                        CreateElement('div', {
                            content: foundOrder.created_at
                        }),
                    ]
                }, foundOrdersContainer);
            });
        }

        UpdateOrderStatuses();
        function UpdateOrderStatuses() {
            let newOrderStatuses = JSON.parse(localStorage.getItem('newOrderStatuses'));
            if (newOrderStatuses !== null) {
                let orders = document.body.querySelectorAll('.order');

                Object.keys(newOrderStatuses).forEach((key) => {

                    let orderStorage = newOrderStatuses[key];

                    orders.forEach((order) => {

                        let orderId = parseInt(order.dataset.orderId);
                        let orderStatusId = parseInt(order.dataset.orderStatusId);

                        if (orderStorage.orderId === orderId) {

                            let orderAlertIcon = order.querySelector('.order-alert-icon');

                            if (orderStorage.oldStatus === orderStatusId) {
                                // orderAlertIcon.hide();
                                // orderAlertIcon.classList.remove('motion');
                            } else {
                                order.dataset.orderStatusId = orderStorage.oldStatus;
                                orderAlertIcon.show();
                                orderAlertIcon.classList.add('motion');
                            }

                        }

                    });

                });
            }

            setTimeout(UpdateOrderStatuses, 10000);
        }

        // function UpdateOrderStatus(orderId) {
        //     let order = document.body.querySelector('[data-order-id="'+orderId+'"]');
        //     let orderAlertIcon = order.querySelector('.order-alert-icon');
        //     orderAlertIcon.hide();
        //     orderAlertIcon.classList.remove('motion');
        // }

        let changeRequiredDateInput = document.body.querySelector('.required-date');
        changeRequiredDateInput.addEventListener('change', (event) => {
            let requiredDate = event.target.value;
            if (requiredDate) {
                location.href = "{{route('manager-arm-orders-page')}}?required-date=" + requiredDate;
            }
        });

        let allOrdersRequiredDateButton = document.body.querySelector('.all-orders-required-date');
        allOrdersRequiredDateButton.addEventListener('click', () => {
            let requiredDate = document.body.querySelector('.required-date');
            location.href = "{{route('manager-arm-orders-page')}}?all-orders=true&required-date=" + requiredDate.value;
        });

        let ordersWithoutCancelledAndCompletedRequiredDateButton = document.body.querySelector('.order-without-cancelled-and-completed-required-date');
        ordersWithoutCancelledAndCompletedRequiredDateButton.addEventListener('click', () => {
            let requiredDate = document.body.querySelector('.required-date');
            location.href = "{{route('manager-arm-orders-page')}}?&required-date=" + requiredDate.value;
        });

    </script>

@stop
