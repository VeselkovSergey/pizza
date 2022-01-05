@extends('app')

@section('content')

    <style>
        input[name="title"] {
            text-transform: uppercase;
        }
    </style>

    <div class="mb-10">
        <a class="orange-button" href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div>
        <div class="mb-10">Промокоды</div>

        <table class="w-100 border table-sort">
            <thead>
            <tr>
                <th>ID</th>
                <th>Значение</th>
                <th>Описание</th>
                <th>Начало</th>
                <th>Конец</th>
                <th>Кол-во</th>
                <th>Использовано</th>
                <th>Активный</th>
                <th>Условие</th>
            </tr>
            </thead>
            <tbody>
            @foreach($promoCodes as $promoCode)
                <tr>
                    <td>{{$promoCode->id}}</td>
                    <td>{{$promoCode->title}}</td>
                    <td>{{$promoCode->description}}</td>
                    <td>{{$promoCode->start_date}}</td>
                    <td>{{$promoCode->end_date}}</td>
                    <td>{{$promoCode->amount}}</td>
                    <td>{{$promoCode->amount_used}}</td>
                    <td>{{$promoCode->active}}</td>
                    <td>{{$promoCode->conditions}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>


@stop

@section('js')

@stop
