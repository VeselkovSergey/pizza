@extends('app')

@section('content')

    <div class="mb-10">
        <a class="orange-button" href="{{route('supplies-page')}}">назад в поставки</a>
    </div>

    <div>
        <div>
            <table class="w-100 border table-sort">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Наименование</th>
                    <th>Количество</th>
                    <th>Стоимость</th>
                    <th>Сумма</th>
                </tr>
                </thead>
                <tbody>
                @foreach($supply->Ingredients as $ingredient)
                    <?php /** @var \App\Models\IngredientsInSupply $ingredient */ ?>
                    <tr>
                        <td>{{$ingredient->id}}</td>
                        <td>{{$ingredient->Ingredient->title}}</td>
                        <td>{{$ingredient->amount_ingredient}}</td>
                        <td>{{$ingredient->price_ingredient}} ₽</td>
                        <td>{{$ingredient->amount_ingredient * $ingredient->price_ingredient}} ₽</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@stop

@section('js')

@stop
