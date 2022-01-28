@extends('app')

@section('content')

    <div class="mb-10">
        <a class="orange-button" href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
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
                    <th>Дата поставки</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ingredients as $ingredient)
                    @php($ingredientLastSupply = $ingredient->LastSupply())
                    <tr>
                        <td>#{{$ingredient->id}}</td>
                        <td>{{$ingredient->title}}</td>
                        <td>{{$ingredientLastSupply->price_ingredient}} ₽</td>
                        <td>{{$ingredientLastSupply->amount_ingredient}}</td>
                        <td>{{$ingredientLastSupply->supply_date}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>


@stop

@section('js')



@stop
