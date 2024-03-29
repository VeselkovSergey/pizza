@extends('app')

@section('content')

    <style>
        .modification:hover {
            background-color: wheat;
        }
    </style>

    <div class="mb-10">
        <a class="orange-button" href="{{route('manager-arm-page')}}">назад в ARM менеджера</a>
    </div>

    <div class="flex-space-between mb-10">
        Редактирование модификаций
        <button class="orange-button save-modifications">Сохранить</button>
    </div>

    <div>
        <table class="w-100 border">
            <thead>
            <tr>
                <th class="w-0">ID</th>
                <th class="w-0">Категория</th>
                <th>Название</th>
                <th class="w-0">Размерность</th>
                <th class="w-0">Стоимость</th>
                <th class="w-0">Стоп лист</th>
            </tr>
            </thead>
            <tbody class="modifications">
            @foreach($productsModifications as $productsModification)
                <tr class="modification">
                    <td class="text-center"># {{$productsModification->id}}</td>
                    <td class="text-center">{{$productsModification->Product->Category->title}}</td>
                    <td>{{$productsModification->Product->title}}</td>
                    <td class="text-center">{{$productsModification->Modification->value}}</td>
                    <td class="text-center">{{$productsModification->selling_price}}</td>
                    <td>
                        <div class="flex-center">
                            <label class="custom-checkbox-label" for="checkbox-{{$productsModification->id}}">
                                <input type="checkbox" id="checkbox-{{$productsModification->id}}" name="stopList[{{$productsModification->id}}]" @if($productsModification->stop_list) checked @endif />
                                <div class="custom-checkbox-slider round"></div>
                            </label>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

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
