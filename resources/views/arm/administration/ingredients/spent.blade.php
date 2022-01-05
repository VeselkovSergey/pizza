@extends('app')

@section('content')

    <style>
        .hover-color:hover {
            background-color: wheat;
        }

        .edit-field {
            cursor: pointer;
            width: -webkit-fill-available;
        }
        .edit-field:not(:read-only) {
            transform: scale(1.2);
        }
    </style>

    <div class="mb-10">
        <a class="orange-button" href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div class="flex-wrap mb-10">
        <div class="mr-10 flex-center">
            <label>
                <span>На какое число</span>
                <input class="start-date" type="date" value="{{$startDate}}">
                <input class="end-date" type="date" value="{{$endDate}}">
            </label>
        </div>
    </div>

    <div>
        <div>
            <div>Потрачено в деньгах : {{$amountSpent}}</div>
        </div>
        <div>
            <table class="w-100 border table-sort">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Наименование</th>
                    <th>Актуальная цена за кг/шт</th>
                    <th>Кол-во в последней поставке</th>
                    <th>Потрачено в еденицах</th>
                    <th>Потрачено в деньгах</th>
                    <th>Дата</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ingredients as $ingredient)
                    <tr class="hover-color ingredient-container" data-ingredient-id="{{$ingredient->id}}">
                        <td>#{{$ingredient->id}}</td>
                        <td><input name="title" class="edit-field" readonly type="text" value="{{$ingredient->title}}"></td>
                        <td>{{$ingredient->last_price_ingredient}} ₽</td>
                        <td>{{$ingredient->last_amount_ingredient}}</td>
                        <td>{{$ingredient->sent}}</td>
                        <td>{{$ingredient->sent * $ingredient->last_price_ingredient}}</td>
                        <td>{{$ingredient->created_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>


@stop

@section('js')

    <script>
        let changeDateFields = document.body.querySelectorAll('.start-date, .end-date');
        changeDateFields.forEach((changeDateField) => {
            changeDateField.addEventListener('change', (event) => {
                let startDate = document.body.querySelector('.start-date').value;
                let endDate = document.body.querySelector('.end-date').value;
                if (startDate && endDate) {
                    location.href = "{{route('administrator-arm-spent-ingredients-page')}}?start-date=" + startDate + "&end-date=" + endDate;
                }
            });
        });

        document.body.querySelectorAll('.edit-field').forEach((field) => {
            field.addEventListener('dblclick', (event) => {
                event.target.removeAttribute('readonly');
            });

            field.addEventListener('blur', (event) => {
                event.target.setAttribute('readonly', 'readonly');
                let ingredientContainer = event.target.closest('.ingredient-container');
                let ingredientId = ingredientContainer.dataset.ingredientId;
                let value = {};
                value[event.target.name] = event.target.value;
                SaveChanges (ingredientId, value);
            });
        });

        function SaveChanges (ingredientId, data) {
            Ajax("{{route('administrator-arm-ingredient-save-changes')}}", "POST", {ingredientId: ingredientId, data: JSON.stringify(data)}).then((response) => {
                FlashMessage(response.message);
            });
        }
    </script>

@stop
