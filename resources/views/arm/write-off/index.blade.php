@extends('app')

@section('content')

    <div class="mb-10">
        <a class="orange-button" href="{{route('write-off-create-page')}}">Новое списание</a>
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
                    <th>Дата списания</th>
                    <th>Кол-во позиций</th>
                    <th>Кто создал</th>
                    <th class="w-0"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($writeOffs as $writeOff)
                    <?php /** @var \App\Models\WriteOff $writeOff */ ?>
                    <tr>
                        <td class="text-center">{{$writeOff->id}}</td>
                        <td>{{$writeOff->date}}</td>
                        <td class="text-center">{{$writeOff->Ingredients->count()}}</td>
                        <td class="text-center">{{$writeOff->Creator->name}}</td>
                        <td class="text-center"><a href="{{route('write-off-detail-page', $writeOff->id)}}">Подробнее</a></td>
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
