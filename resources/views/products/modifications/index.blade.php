@extends('app')

@section('content')

    <style>
        body {
            background-color: whitesmoke!important;
            color: black!important;
        }
        table, th, td {
            border: 1px solid black;
        }
        .flash-message-container .flash-message-text {
            border-radius: 5px;
            background-color: rgba(0, 0, 0, 0.9);
            color: #ffffff;
        }
    </style>

    <div class="flex-space-between mb-10">
        Редактирование модификаций
        <button class="bg-orange save-modifications">Сохранить</button>
    </div>

    <table class="w-100 border">
        <thead>
        <tr>
            <th>ID</th>
            <th>Категория</th>
            <th>Название</th>
            <th>Размерность</th>
            <th>Стоимость</th>
            <th>Стоп лист</th>
        </tr>
        </thead>
        <tbody class="modifications">
        @foreach($productsModifications as $productsModification)
            <tr>
                <td># {{$productsModification->id}}</td>
                <td>{{$productsModification->Product->Category->title}}</td>
                <td>{{$productsModification->Product->title}}</td>
                <td>{{$productsModification->Modification->value}}</td>
                <td>{{$productsModification->selling_price}}</td>
                <td>
                    <input type="checkbox" name="stopList[{{$productsModification->id}}]" @if($productsModification->stop_list) checked @endif>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@stop

@section('js')

    <script>

        let saveModificationButton = document.body.querySelector('.save-modifications');
        saveModificationButton.addEventListener('click', () => {
            let data = GetDataFormContainer('modifications');
            Ajax('{{route('manager-arm-products-modifications-save')}}', 'POST', data).then((response) => {
                FlashMessage(response.message);
            });
        });

    </script>

@stop
