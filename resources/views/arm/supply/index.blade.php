@extends('app')

@section('content')

    <div class="mb-10">
        <a class="orange-button" href="{{route('supply-create-page')}}">Новая поставка</a>
    </div>

    <div>
        <div class="mb-10">
            <label for="">Фильтр по ингредиенту
                <select name="ingredients" class="w-100">
                    <option value="0">Все</option>
                    @foreach($ingredients as $ingredient)
                    <option value="{{$ingredient->id}}">{{$ingredient->title}}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div>
            <table class="w-100 border table-sort">
                <thead>
                <tr>
                    <th class="text-center w-0">#</th>
                    <th>Поставщик</th>
                    <th>Дата поставки</th>
                    <th>Тип оплаты</th>
                    <th>Сумма</th>
                    <th>Кол-во позиций</th>
                    <th>Кто создал</th>
                    <th class="w-0"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($supplies as $supply)
                    <?php /** @var \App\Models\Supply $supply */ ?>
                    <tr class="hover-color">
                        <td class="text-center">{{$supply->id}}</td>
                        <td class="text-center">{{$supply->Supplier->title}}</td>
                        <td>{{$supply->supply_date}}</td>
                        <td class="text-center">{{$supply->PaymentType()}}</td>
                        <td class="text-center">{{$supply->SupplySum()}}</td>
                        <td class="text-center">{{$supply->Ingredients->count()}}</td>
                        <td class="text-center">{{$supply->Creator->name}}</td>
                        <td class="text-center"><a href="{{route('supply-detail-page', $supply->id)}}">Подробнее</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@stop

@section('js')

    <script>
        const ingredientSelector = document.body.querySelector('select[name="ingredients"]');
        SelectWithSearch(ingredientSelector);
        ingredientSelector.addEventListener('change', (event) => {
            let ingredientId = event.target.value;
            location.href = "{{route('supplies-page')}}?ingredient=" + ingredientId;
        });
    </script>

@stop
