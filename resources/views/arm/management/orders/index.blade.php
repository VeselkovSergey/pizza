@extends('app')

@section('content')

    <style>
        .order:hover {
            transform: scale(1.005);
        }
        .hover-show {
            display: none;
        }
        .order:hover .hover-show {
            display: flex;
        }
        .order-info > div {
            border-right: 1px solid;
        }
        @media screen and (max-width: 540px) {
            .order-info > div {
                border-right: unset;
                width: 100%;
            }
        }
    </style>

    <div>
        <div>Заказы</div>
        <div class="mb-10 flex-wrap">
            <label class="mb-5 mr-5 w-fit">
                Поиск по номеру телефона:
                <input class="search-orders-by-phone" type="text" placeholder="79991112233" maxlength="11">
            </label>
            <button class="orange-button"><a href="{{route('manager-arm-orders-page')}}?all-orders=true">показать выполненные/отказанные</a></button>
            <button class="orange-button"><a href="{{route('manager-arm-orders-page')}}">скрыть выполненные/отказанные</a></button>
        </div>
        <div class="flex-column">
            <div class="orders-container" style="order: 2">
                @php($sum = 0)
                @php($sumCash = 0)
                @php($sumBank = 0)
                @foreach($orders as $order)
                    @php($clientInfo = json_decode($order->client_raw_data))

                    @if(!$order->IsCancelled())
                        @php($sum += $order->order_amount)
                        @if($clientInfo->typePayment[0] === false)
                            @php($sumCash += $order->order_amount)
                        @else
                            @php($sumBank += $order->order_amount)
                        @endif
                    @endif

                    <a href="{{route('manager-arm-order-page', $order->id)}}" data-order-id="{{$order->id}}" data-order-status-id="{{$order->status_id}}" class="order flex-space-between clear-a border p-10 m-5 order-status-{{$order->status_id}}">
                        <div>
                            <div># {{$order->id}} {{\App\Models\Orders::STATUS[$order->status_id]}} {{$order->CurrentStatus()->created_at}}</div>
                            <div class="order-info flex-wrap mt-10 @if((\App\Models\Orders::STATUS_TEXT['cancelled'] === $order->status_id) || \App\Models\Orders::STATUS_TEXT['completed'] === $order->status_id) hover-show @endif">
                                <div class="mb-10 px-25">
                                    <div>Имя: {{$clientInfo->clientName}}</div>
                                    <div>Номер: {{$clientInfo->clientPhone}}</div>
                                </div>
                                <div class="mb-10 px-25">
                                    <div>Адрес: {{$clientInfo->clientAddressDelivery}}</div>
                                    <div>Комментарий: {{$clientInfo->clientComment}}</div>
                                </div>
                                <div class="mb-10 px-25">
                                    <div>Сумма: {{$order->order_amount}} ₽</div>
                                    <div>Оплата: {{($clientInfo->typePayment[0] === true ? 'Карта' : 'Наличные')}}</div>
                                </div>
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
            <div style="order: 1;">
                <div class="mb-10">Итого: {{$sum}} ₽ (Наличные: {{$sumCash}} ₽ / Банк: {{$sumBank}} ₽)</div>
            </div>
        </div>
        <div class="found-orders-container hide"></div>
    </div>

@stop

@section('js')

    <script>

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

        function MarkOrderNewStatus(orderId, oldStatusId, newStatusId) {
            let orderContainer = ordersContainer.querySelector('[data-order-id="' + orderId + '"]');
            if (orderContainer) {

                let orderAlertIcon = orderContainer.querySelector('.order-alert-icon');
                orderAlertIcon.classList.add('motion');
                orderAlertIcon.show();

                orderContainer.addEventListener('click', () => {
                    orderAlertIcon.classList.remove('motion');
                    orderAlertIcon.hide();
                    ManagerArmCheckOrderStatusChange();
                });
            }
        }

    </script>

@stop
