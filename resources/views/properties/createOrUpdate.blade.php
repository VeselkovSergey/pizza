@extends('app')

@section('content')

    <div>

        <form class="properties-create-or-edit-form" action="" onsubmit="return false;">

            <div>
                <label for="">Название</label>
                <input class="need-validate" name="title" type="text">
            </div>
            <div>
                <label for="">Тип</label>
                <select name="propertyType">
                    @foreach($typesProperties as $typeProperty)
                        <option value="{{$typeProperty->id}}">{{$typeProperty->title . ' - ' . $typeProperty->value_unit}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="">Значение</label>
                <input class="need-validate" name="propertyValue" type="text">
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
            let propertyType = document.body.querySelector('select[name="propertyType"]').value;
            let propertyValue = document.body.querySelector('input[name="propertyValue"]').value;
            let data = {
                title: title,
                propertyType: propertyType,
                propertyValue: propertyValue,
            }

            if (!CheckingFieldForEmptiness('properties-create-or-edit-form', true)) {
                return;
            }

            Ajax("{{route('property-save')}}", 'POST', data).then((response) => {
                FlashMessage(response.message);
            })
        });

    </script>

@stop
