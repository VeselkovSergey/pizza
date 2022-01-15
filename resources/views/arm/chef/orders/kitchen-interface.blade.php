@extends('app')

@section('content')

    <style>
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
            @foreach($orders as $order)
                <div class="w-25 flex-column order-id-{{$order->id}}" onclick="OrderCompletionWindow({{$order->id}})">
                    @php($clientInfo = json_decode($order->client_raw_data))
                    @php($productsRawData = json_decode($order->products_raw_data))


                    <div class="p-5">
                        <div class="border p-5">
                            <div class="flex-space-between mb-10">
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
        </div>
    </div>

@stop

@section('js')

    <script>

        function OrderCompletionWindow() {
            let completeButton = CreateElement('button', {content: 'выполнен', class: 'big-button orange-button'});
            ModalWindow(completeButton);
        }

    </script>

@stop
