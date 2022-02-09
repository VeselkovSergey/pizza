@extends('app')

@section('content')

    <style>
        ::-webkit-scrollbar-thumb {
            background-color: #ff7300;
            border-radius: 2px;
            border: 1px solid hsla(0,0%,50.2%,.1);
        }
        ::-webkit-scrollbar-track {
            background: hsla(0,0%,50.2%,.1);
        }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
    </style>

    <div class="flex pl-10">
        <h3 class="mr-25"><a class="clear-a {{request()->is('profile') ? 'color-orange' : ''}}" href="{{route('profile')}}">Профиль</a></h3>
        <h3><a class="clear-a {{request()->is('profile/orders') ? 'color-orange' : ''}}" href="{{route('profile-orders')}}">Заказы</a></h3>
    </div>
    <div class="flex-wrap">
        @foreach($userOrders as $order)
            <?php /** @var \App\Models\Orders $order */ ?>
        @php($products = $order->ProductsModifications)
            <div class="mb-10 w-100">
                <div class=" border-orange border-radius-15 p-10" style="background-color: #00000090">
                    <div class="mb-10">
                        <div class="mb-10">{{\App\Helpers\StringHelper::DateBeautifulFormat($order->created_at)}}</div>
                        <div class="order-status-{{$order->status_id}} w-fit py-5 px-10 border-radius-10">Статус: {{$order->CurrentStatusText()}}</div>
                    </div>
                    <div class="flex scroll-x-auto w-100 mb-10">
                        @foreach($products as $product)
                            @php($modification = $product->ProductModifications->Modification)
                            @php($productModel = $product->ProductModifications->Product)
                            @php($productTitle = $productModel->title . ' ' . ($modification->title !== 'Соло-продукт' ? $modification->title . ' ' . $modification->value : ''))
                            @php($imgFile = asset('img/png/'. $productModel->id .'.png'))
                            @for($i = 0; $i < $product->product_modification_amount; $i++)
                                <picture style="width: 100px; min-width: 100px;">
                                    <source class="w-100" srcset="{{url($imgFile)}}" type="image/webp">
                                    <source class="w-100" srcset="{{url($imgFile)}}" type="image/jpeg">
                                    <img class="w-100" src="{{url($imgFile)}}" alt="{{$productTitle}}">
                                </picture>
                            @endfor
                        @endforeach
                    </div>
                    <div>
                        <div>
                            <div>Сумма без скидки: {{$order->total_order_amount}}</div>
                        </div>
                        <div>
                            <div>Сумма: {{$order->order_amount}}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@stop

@section('js')

@stop
