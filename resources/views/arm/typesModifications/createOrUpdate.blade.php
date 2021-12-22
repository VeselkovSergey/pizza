@extends('app')

@section('content')

    <div>

        <form class="modification-typen-create-or-edit-form" action="" onsubmit="return false;">

            <div>
                <label for="">Название
                    <input class="need-validate" name="title" type="text">
                </label>
            </div>
            <div>
                <label for="">Значение
                    <input class="need-validate" name="value_unit" type="text">
                </label>
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

            let title = document.body.querySelector('input[name="title"]');
            let titleValue = title.value;
            let valueUnit = document.body.querySelector('input[name="value_unit"]');
            let valueUnitValue = valueUnit.value;
            let data = {
                title: titleValue,
                valueUnit:valueUnitValue
            }

            if (!CheckingFieldForEmptiness('modification-type-create-or-edit-form', true)) {
                return;
            }

            Ajax("{{route('modification-type-save')}}", 'POST', data).then((response) => {
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
