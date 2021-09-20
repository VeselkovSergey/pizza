@extends('app')

@section('content')

    <div>

        <form class="product-create-or-edit-form" action="" onsubmit="return false;">

            <div>
                <label for="">Название</label>
                <input class="need-validate" name="title" type="text">
            </div>

            <div>Свойства</div>
            @foreach($propertiesForProducts as $propertyForProducts)
                <label for="">{{$propertyForProducts->title . ' - ' . $propertyForProducts->value . ' ' . $propertyForProducts->Type->value_unit}}</label>
                <input type="checkbox">
            @endforeach

            <div>
                <button class="save-button">Создать</button>
            </div>

        </form>

    </div>

@stop

@section('js')

    <script>

        let saveButton = document.body.querySelector('.save-button');
        saveButton.addEventListener('click', () => {

            let title = document.body.querySelector('input[name="title"]').value;

            if (!CheckingFieldForEmptiness('product-create-or-edit-form', true)) {
                return;
            }

            Ajax("{{route('product-save')}}", 'POST', {title: title}).then((response) => {
                FlashMessage(response.message);
            })
        });

    </script>

@stop
