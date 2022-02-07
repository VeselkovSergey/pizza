@extends('app')

@section('content')

    <style>
        .hover-color:hover {
            background-color: wheat;
        }
    </style>

    <div class="mb-10">
        <a class="orange-button" href="{{route('write-offs-page')}}">назад в поставки</a>
        <a class="orange-button" href="{{route('write-off-edit-page', $writeOff->id)}}">Редактировать</a>
    </div>

    <div>

        <div class="flex-column">
            <div>{{$writeOff->created_at}}</div>
            <div class="mb-10">{{$writeOff->Creator->name}}</div>
            <div>
                <table class="w-100 border table-sort">
                    <thead>
                    <tr>
                        <th class="text-center w-0">#</th>
                        <th>Наименование</th>
                        <th class="w-0">Количество</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($writeOff->Ingredients as $ingredient)
                        <?php /** @var \App\Models\IngredientsInWriteOff $ingredient */ ?>
                        <tr class="hover-color">
                            <td>{{$ingredient->ingredient_id}}</td>
                            <td>{{$ingredient->Ingredient->title}}</td>
                            <td class="text-center">{{$ingredient->amount_ingredient}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
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
