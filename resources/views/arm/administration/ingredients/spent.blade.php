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
            transform: scale(1.01);
        }
        .used:hover {
            color: #7300ff;
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
                    <th class="w-0">ID</th>
                    <th>Наименование</th>
                    <th>Описание</th>
                    <th class="w-0">Актуальная цена за кг/шт</th>
                    <th class="w-0">Сумма закупки</th>
                    <th class="w-0">Потрачено в деньгах</th>
                    <th class="w-0">Кол-во закупки</th>
                    <th class="w-0">Потрачено в единицах</th>
                    <th class="w-0">Остаток</th>
                    <th class="w-0"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($ingredients as $ingredient)
                    @php($ingredientLastPrice = $ingredient->CurrentPrice())
                    @php($balance = round($ingredient->quantityPurchased - $ingredient->sent, 2))
                    <tr class="hover-color ingredient-container" data-ingredient-id="{{$ingredient->id}}">
                        <td class="text-center">#{{$ingredient->id}}</td>
                        <td><input name="title" class="edit-field" readonly type="text" value="{{$ingredient->title}}"></td>
                        <td><input name="description" class="edit-field" readonly type="text" value="{{$ingredient->description}}"></td>
                        <td class="text-center">{{$ingredientLastPrice}}</td>
                        <td class="text-center">{{round($ingredient->quantityPurchased * $ingredientLastPrice, 2)}}</td>
                        <td class="text-center">{{round($ingredient->sent * $ingredientLastPrice, 2)}}</td>
                        <td class="text-center">{{$ingredient->quantityPurchased}}</td>
                        <td class="text-center">{{$ingredient->sent}}</td>
                        <td class="text-center @if($balance <= 0) bg-red @endif">{{$balance}}</td>
                        <td class="text-center used cp">Где юзается</td>
                        <td class="text-center supply cp">Поставки</td>
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
                    LoaderShow();
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

        document.body.querySelectorAll('.used').forEach((ingredient) => {
            ingredient.addEventListener('click', () => {
                const ingredientId = ingredient.closest('.ingredient-container').dataset.ingredientId;
                Ajax("{{route('products-used-ingredient')}}?ingredientId=" + ingredientId, 'GET').then((response) => {
                    ModalWindow(GenerationProductsUsedIngredient(response));
                });
            });
        });

        function GenerationProductsUsedIngredient(data) {
            let content = '<table class="white-border"><thead><tr><th>Наименование</th><th>Кол-во</th></tr></thead><tbody>';
            Object.keys(data).forEach((key) => {
                const product = data[key];
                content += '<tr><td>'+product.productTitle+'</td><td class="text-center">'+product.ingredientUsageAmount+'</td></tr>'
            });
            return content += '</tbody></table>';
        }

        document.body.querySelectorAll('.supply').forEach((ingredient) => {
            ingredient.addEventListener('click', () => {
                const ingredientId = ingredient.closest('.ingredient-container').dataset.ingredientId;
                Ajax("{{route('ingredient-supply')}}?ingredientId=" + ingredientId, 'GET').then((response) => {
                    let modal = ModalWindow(GenerationIngredientSupply(response));

                    modal.querySelectorAll('.edit-field').forEach((field) => {
                        field.addEventListener('dblclick', (event) => {
                            event.target.removeAttribute('readonly');
                        });

                        field.addEventListener('blur', (event) => {
                            event.target.setAttribute('readonly', 'readonly');
                            let supplyContainer = event.target.closest('.supply-container');
                            let ingredientInSupplyId = supplyContainer.dataset.ingredientInSupplyId;
                            let value = {};
                            value[event.target.name] = event.target.value.replace(/,/, '.');
                            SaveChangesSupply(ingredientInSupplyId, value);
                        });
                    });

                });
            });
        });

        function GenerationIngredientSupply(data) {
            let content = '<div class="mb-10">'+data[0].title+'</div><table class="white-border"><thead><tr><th>Дата</th><th>Кол-во</th><th>Цена за единицу</th><th>В поставку</th></tr></thead><tbody>';
            Object.keys(data).forEach((key) => {
                const supply = data[key];
                content += '<tr class="supply-container" data-ingredient-in-supply-id="'+supply.id+'"><td>'+supply.date+'</td><td class="text-center">'+supply.amount+'</td><td class="text-center"><input name="price_ingredient" class="edit-field" readonly type="text" value="'+supply.price+'"></td><td class="text-center cp"><a class="color-orange" target="_blank" href="{{route('supply-detail-page')}}/'+supply.supplyId+'">тык</a></td></tr>'
            });
            return content += '</tbody></table>';
        }

        function SaveChangesSupply(ingredientInSupplyId, data) {
            Ajax("{{route('administrator-arm-ingredient-in-supply-save-changes')}}", "POST", {ingredientInSupplyId: ingredientInSupplyId, data: JSON.stringify(data)}).then((response) => {
                FlashMessage(response.message);
            });
        }
    </script>

@stop
