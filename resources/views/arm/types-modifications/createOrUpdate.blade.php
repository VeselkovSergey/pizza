@extends('app')

@section('content')

    <div class="mb-10 flex-wrap">
        <a class="orange-button" href="{{route('types-modifications-page')}}">назад</a>
        <button class="save-button orange-button ml-a">Создать</button>
    </div>

    <div class="container">
        <div class="mb-10">
            <label for="">Название
                <input class="need-validate" name="title" type="text">
            </label>
        </div>
        <div class="mb-10">
            <label for="">Значение
                <input class="need-validate" name="value_unit" type="text">
            </label>
        </div>
    </div>

@stop

@section('js')

    <script>

        let saveButton = document.body.querySelector('.save-button');
        saveButton.addEventListener('click', () => {

            let title = document.body.querySelector('input[name="title"]');
            let titleValue = title.value;
            let valueUnit = document.body.querySelector('input[name="value_unit"]');
            let valueUnitValue = valueUnit.value;
            let data = {
                title: titleValue,
                valueUnit:valueUnitValue
            }

            if (!CheckingFieldForEmptiness('container', true)) {
                return;
            }

            Ajax("{{route('type-modification-save')}}", 'POST', data).then((response) => {
                FlashMessage(response.message);
                if (response.status === true) {
                    title.value = '';
                    valueUnit.value = '';
                    title.focus();
                }
            })
        });

    </script>

@stop
