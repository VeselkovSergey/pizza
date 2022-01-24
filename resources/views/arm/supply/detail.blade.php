@extends('app')

@section('content')

    <div class="mb-10">
        <a class="orange-button" href="{{route('supplies-page')}}">назад в поставки</a>
        @if(auth()->user()->IsAdmin())
            <a class="orange-button" href="{{route('supply-edit-page', $supply->id)}}">Редактировать</a>
        @endif
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
                @php($dell = '')
                @foreach($supply->Ingredients as $ingredient)
                    <?php /** @var \App\Models\IngredientsInSupply $ingredient */ ?>
                    <tr>
                        <td>{{$ingredient->id}}</td>
                        @if(empty($ingredient->Ingredient))
                            @php($dell += $ingredient->ingredient_id . ',')
                            <td>{{$ingredient->ingredient_id}}</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        @else
                            <td>{{$ingredient->Ingredient->title}}</td>
                            <td>{{$ingredient->amount_ingredient}}</td>
                            <td>{{$ingredient->price_ingredient}} ₽</td>
                            <td>{{$ingredient->amount_ingredient * $ingredient->price_ingredient}} ₽</td>
                        @endif

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

    {{$dell}}


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
