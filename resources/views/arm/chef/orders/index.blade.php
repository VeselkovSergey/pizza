@extends('app')

@section('content')

    <style>
        .order:hover {
            transform: scale(1.01);
        }
    </style>

    <div>
        <div>Заказы</div>
        <div class="orders-container">
            @foreach($orders as $order)
                @php($clientInfo = json_decode($order->client_raw_data))

                <a href="{{route('chef-arm-order-page', $order->id)}}" data-order-id="{{$order->id}}" data-order-status-id="{{$order->status_id}}" class="order flex-space-between clear-a border p-10 m-5 order-status-{{$order->status_id}}">
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
    </div>

@stop

@section('js')

@stop
