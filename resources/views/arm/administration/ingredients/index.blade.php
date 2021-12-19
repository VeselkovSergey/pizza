@extends('app')

@section('content')

    <div class="mb-10">
        <a href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div>
        <div>
            <table class="w-100 border table-sort">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Наименование</th>
                    <th>Актуальная цена за кг/шт</th>
                    <th>Кол-во в последней поставке</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ingredients as $ingredient)
                    <tr>
                        <td>#{{$ingredient->id}}</td>
                        <td>{{$ingredient->title}}</td>
                        <td>{{$ingredient->last_price_ingredient}} ₽</td>
                        <td>{{$ingredient->last_amount_ingredient}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>


@stop

@section('js')



@stop
