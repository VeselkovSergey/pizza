@extends('app')

@section('content')

    <style>
        header {
            display: none!important;
        }
        .width-order-info {
            width: 25%;
        }
        @media screen and (max-width: 720px) {
            .width-order-info {
                width: 50%;
            }
        }
        @media screen and (max-width: 540px) {
            .width-order-info {
                width: 100%;
            }
        }
        .title-order {
            background-color: #f1848e;
        }
        .big-button {
            font-size: 35px;
        }
        .bg-order-2 {
            background-color: #ffc107;
        }
        .bg-order-4 {
            background-color: #84b79f;
        }
        .bg-order-6 {
            background-color: #b0e9f5;
        }
    </style>

    <div>
        <div>Заказы</div>
        <div class="orders-container flex-wrap">
            @if(sizeof($orders))
                @foreach($orders as $order)
                    <div class="width-order-info mb-25 flex-column order-id-{{$order->id}}" onclick="OrderCompletionWindow({{$order->id}})">
                        @php($clientInfo = json_decode($order->client_raw_data))
                        @php($productsRawData = json_decode($order->products_raw_data))


                        <div class="p-5">
                            <div class="border p-5">
                                <div class="title-order flex-space-between mb-10">
                                    <div class="font-weight-600"># {{$order->id}}</div>
                                    <div class="font-weight-600">На кухне с {{$order->CurrentStatus()->created_at->format('H:m')}}</div>
                                </div>

                                <div class="products-container flex-column">

                                    <div class="font-weight-600" style="order: 1;">Пиццы:</div>
                                    <div class="font-weight-600 mt-25" style="order: 3;">Горячка:</div>
                                    <div class="font-weight-600 mt-25" style="order: 5;">Остальное:</div>

                                    @foreach($productsRawData as $product)

                                        <?php

                                        $flexOrder = match ($product->data->product->categoryId) {
                                            1 => 2,
                                            2, 3, 4, => 4,
                                            default => 6,
                                        };

                                        ?>

                                        <div class="flex-space-between py-5 bg-order-{{$flexOrder}}" style="border-bottom: 1px solid black; order: {{$flexOrder}}">
                                            <span>{{$product->data->product->categoryTitle . ' ' . $product->data->product->title}}</span>
                                            <span class="font-weight-600">{{$product->amount}}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

@stop

@section('js')

    <script>

        Start();
        function Start() {
            let completeButton = CreateElement('button', {content: 'Понеслась;)', class: 'big-button orange-button'});
            let modal = ModalWindow(completeButton);
            completeButton.addEventListener('click', () => {
                modal.hide();
                document.body.classList.remove('scroll-off')
            });
        }

        function OrderCompletionWindow(orderId) {
            let completeButton = CreateElement('button', {content: 'выполнен', class: 'big-button orange-button'});
            let modal = ModalWindow(completeButton);
            completeButton.addEventListener('click', () => {
                Ajax('{{route('chef-arm-change-status-order-to-cooked')}}', 'POST', {orderId: orderId}).then((response) => {
                    document.body.querySelector('.order-id-'+orderId).remove();
                    modal.hide();
                    document.body.classList.remove('scroll-off')
                });
            });
        }

        const ordersContainer = document.body.querySelector('.orders-container');

        const kitchenChannel = pusher.subscribe('kitchen-channel');
        kitchenChannel.bind('newOrderForKitchen', function(data) {

            let audio = new Audio('{{asset('audio/new-order.mp3')}}'); // Создаём новый элемент Audio
            audio.play(); // Автоматически запускаем

            Ajax('{{route('order-info')}}?orderId=' + data.orderId).then((response) => {
                GenerateOrderInfo(response);
            });
        });

        function GenerateOrderInfo(orderInfo) {
            let content =
                                '<div class="p-5">'+
                                    '<div class="border p-5">'+
                                        '<div class="title-order flex-space-between mb-10">'+
                                            '<div class="font-weight-600"># ' + orderInfo.id +'</div>'+
                                            '<div class="font-weight-600">На кухне с '+orderInfo.sendToKitchen+'</div>'+
                                        '</div>'+
                                        '<div class="products-container flex-column">'+
                                            '<div class="font-weight-600" style="order: 1;">Пиццы:</div>'+
                                            '<div class="font-weight-600 mt-25" style="order: 3;">Горячка:</div>'+
                                            '<div class="font-weight-600 mt-25" style="order: 5;">Остальное:</div>'+
                                            GenerateProductInfo(orderInfo) +
                                        '</div>'+
                                    '</div>'+
                                '</div>';

            return CreateElement('div', {content: content, class: 'width-order-info mb-25 flex-column order-id-'+orderInfo.id, attr: {onclick: 'OrderCompletionWindow('+orderInfo.id+')'}}, ordersContainer);
        }

        function GenerateProductInfo(orderInfo) {
            let content = '';
            Object.keys(orderInfo.products).forEach((key) => {
                let product = orderInfo.products[key];
                let flexOrder = product.categoryId === 1 ? 2 : ([2, 3, 4].indexOf(product.categoryId) !== -1 ? 4 : 6);
                content +=
                    '<div class="flex-space-between py-5 bg-order-'+flexOrder+'" style="border-bottom: 1px solid black; order: '+flexOrder+'">'+
                        '<span>'+product.title+'</span>'+
                        '<span class="font-weight-600">'+product.amount+'</span>'+
                    '</div>';
            });
            return content;
        }

    </script>

@stop
