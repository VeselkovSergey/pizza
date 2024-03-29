@extends('app')

@section('content')

    <div>

        <form class="modifications-create-or-edit-form" action="" onsubmit="return false;">

            <div class="mb-10">
                <label for="">Название
                    <input class="need-validate" name="title" type="text">
                </label>
            </div>
            <div class="mb-10">
                <label for="">Тип
                    <select name="modificationType">
                        @foreach($typeModifications as $typeModification)
                            <option value="{{$typeModification->id}}">{{$typeModification->title . ' - ' . $typeModification->value_unit}}</option>
                        @endforeach
                    </select>
                </label>

            </div>
            <div class="mb-10">
                <label for="">Значение
                    <input class="need-validate" name="modificationValue" type="text">
                </label>
            </div>
            <div>
                <button class="save-button orange-button">Создать</button>
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
            let modificationType = document.body.querySelector('select[name="modificationType"]');
            let modificationTypeValue = modificationType.value;
            let modificationValue = document.body.querySelector('input[name="modificationValue"]');
            let modificationValueValue = modificationValue.value;
            let data = {
                title: titleValue,
                modificationType: modificationTypeValue,
                modificationValue: modificationValueValue,
            }

            if (!CheckingFieldForEmptiness('modifications-create-or-edit-form', true)) {
                return;
            }

            Ajax("{{route('modification-save')}}", 'POST', data).then((response) => {
                FlashMessage(response.message);
                if (response.status === true) {
                    title.value = '';
                    modificationValueValue.value = '';
                    title.focus();
                }
            })
        });

    </script>

@stop
