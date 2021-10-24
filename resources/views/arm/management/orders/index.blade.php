@extends('app')

@section('content')

    <style>
        .order:hover {
            transform: scale(1.01);
        }
    </style>

    <div>
        <div>Заказы</div>
        <div>
            <label>
                Поиск по номеру телефона:
                <input class="search-orders-by-phone" type="text" placeholder="79991112233" maxlength="11">
            </label>
        </div>
        <div class="orders-container">
            @foreach($orders as $order)
                <a href="{{route('manager-arm-order-page', $order->id)}}" class="order block clear-a border p-10 m-5 order-status-{{$order->status_id}}">
                    <div>{{\App\Models\Orders::STATUS[$order->status_id]}}</div>
                    <div>{{$order->created_at}}</div>
                </a>
            @endforeach
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
            if (value.length === 11) {
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

    </script>

@stop