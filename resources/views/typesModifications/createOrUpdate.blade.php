@extends('app')

@section('content')

    <div>

        <form class="modification-typen-create-or-edit-form" action="" onsubmit="return false;">

            <div>
                <label for="">Название</label>
                <input class="need-validate" name="title" type="text">
            </div>
            <div>
                <label for="">Значение</label>
                <input class="need-validate" name="value_unit" type="text">
            </div>
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
            let valueUnit = document.body.querySelector('input[name="value_unit"]').value;
            let data = {
                title: title,
                valueUnit:valueUnit
            }

            if (!CheckingFieldForEmptiness('modification-type-create-or-edit-form', true)) {
                return;
            }

            Ajax("{{route('modification-type-save')}}", 'POST', data).then((response) => {
                FlashMessage(response.message);
            })
        });

    </script>

@stop
