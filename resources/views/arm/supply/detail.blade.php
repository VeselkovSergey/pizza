@extends('app')

@section('content')

    <style>
        .hover-color:hover {
            background-color: wheat;
        }
    </style>

    <div class="mb-10">
        <a class="orange-button" href="{{route('supplies-page')}}">назад в поставки</a>
        <a class="orange-button" href="{{route('supply-edit-page', $supply->id)}}">Редактировать</a>
    </div>

    <div>

        @if(sizeof($files))

        <div>
            <div>Файлы</div>
            <div class="flex-wrap">
                @foreach($files as $file)
                    @if($file)
                        <div>
                            @if($file->modelFile->extension === 'jpg')
                                <img class="open-large cp" width="200" height="200" src="{{route('files', $file->modelFile->id)}}">
                            @endif
                            <div>
                                <a href="{{route('files', $file->modelFile->id)}}" download="{{$file->modelFile->original_name}}">{{$file->modelFile->original_name . '.' . $file->modelFile->extension}}</a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        @endif

        <div class="flex-column">
            <div style="order: 2">
                <table class="w-100 border table-sort">
                    <thead>
                    <tr>
                        <th class="text-center w-0">#</th>
                        <th>Наименование</th>
                        <th class="w-0">Количество</th>
                        <th class="w-0">Стоимость</th>
                        <th class="w-0">Сумма</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($sum = 0)
                    @foreach($supply->Ingredients as $ingredient)
                        <?php /** @var \App\Models\IngredientsInSupply $ingredient */ ?>
                        <tr class="hover-color">
                            <td>{{$ingredient->ingredient_id}}</td>
                            @if(empty($ingredient->Ingredient))
                                <td>{{$ingredient->ingredient_id}}</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            @else
                                @php($sum += $ingredient->amount_ingredient * $ingredient->price_ingredient)
                                <td>{{$ingredient->Ingredient->title}}</td>
                                <td class="text-center">{{$ingredient->amount_ingredient}}</td>
                                <td class="text-center">{{round($ingredient->price_ingredient, 2)}} ₽</td>
                                <td class="text-center">{{round($ingredient->amount_ingredient * $ingredient->price_ingredient, 2)}} ₽</td>
                            @endif

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <h3 style="order: 1">Сумма: {{round($sum, 2)}} ₽</h3>
            <div style="order: 1">{{$supply->supply_date}}</div>
            <div style="order: 1" class="mb-10">{{$supply->Creator->name}}</div>
        </div>

    </div>


@stop

@section('js')

    <script>

        document.body.querySelectorAll('.open-large').forEach((imgElement) => {
           imgElement.addEventListener('click', (event) => {
               let imgUrl = event.target.getAttribute('src');
               let img = CreateElement('img', {
                   attr: {
                       src: imgUrl
                   }
               });
               ModalWindow(img);
           })
        });

    </script>

@stop
