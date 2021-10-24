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
                <a href="{{route('chef-arm-order-page', $order->id)}}" class="order block clear-a border p-10 m-5 order-status-{{$order->status_id}}">
                    <div>{{\App\Models\Orders::STATUS[$order->status_id]}}</div>
                    <div>{{$order->created_at}}</div>
                </a>
            @endforeach
        </div>
    </div>

@stop

@section('js')

@stop