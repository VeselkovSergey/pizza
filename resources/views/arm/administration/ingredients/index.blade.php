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
                    <th class="w-0">ID</th>
                    <th>Наименование</th>
                    <th class="w-0">Актуальная цена за кг/шт</th>
                    <th class="w-0">Кол-во в последней поставке</th>
                    <th class="w-10">Дата поставки</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ingredients as $ingredient)
                    @php($ingredientLastSupply = $ingredient->LastSupply())
                    <tr class="hover-color">
                        <td class="text-center">#{{$ingredient->id}}</td>
                        <td>{{$ingredient->title}}</td>
                        <td class="text-center">{{$ingredientLastSupply->price_ingredient}} ₽</td>
                        <td class="text-center">{{$ingredientLastSupply->amount_ingredient}}</td>
                        <td class="text-center">{{$ingredientLastSupply->supply_date}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>


@stop

@section('js')



@stop
