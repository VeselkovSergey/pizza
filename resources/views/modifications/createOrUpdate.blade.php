@extends('app')

@section('content')

    <div>

        <form class="modifications-create-or-edit-form" action="" onsubmit="return false;">

            <div>
                <label for="">Название</label>
                <input class="need-validate" name="title" type="text">
            </div>
            <div>
                <label for="">Тип</label>
                <select name="modificationType">
                    @foreach($typeModifications as $typeModification)
                        <option value="{{$typeModification->id}}">{{$typeModification->title . ' - ' . $typeModification->value_unit}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="">Значение</label>
                <input class="need-validate" name="modificationValue" type="text">
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
            let modificationType = document.body.querySelector('select[name="modificationType"]').value;
            let modificationValue = document.body.querySelector('input[name="modificationValue"]').value;
            let data = {
                title: title,
                modificationType: modificationType,
                modificationValue: modificationValue,
            }

            if (!CheckingFieldForEmptiness('modifications-create-or-edit-form', true)) {
                return;
            }

            Ajax("{{route('modification-save')}}", 'POST', data).then((response) => {
                FlashMessage(response.message);
            })
        });

    </script>

@stop
