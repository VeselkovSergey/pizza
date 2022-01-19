@extends('app')

@section('content')

    <style>
        .select-with-search {
            display: none;
        }
        .search-field-container {
            width: 100%;
            position: relative;
        }
        .search-field {
            width: calc(100% - 20px);
        }
        .custom-options-container {
            position: absolute;
            background-color: white;
            width: calc(100% - 20px);
            padding: 5px 10px;
            border: 1px solid #cbcdd1;
            border-radius: 5px;
            max-height: 250px;
            overflow: auto;
        }
        .custom-option {
            padding: 3px;
        }
        .custom-option:hover {
            background-color: #4489c8;
        }
    </style>

    <style>

        .delete-ingredient-button {
            padding: 15px;
            margin: 0;
            border: 1px solid red;
            color: red;
            background-color: unset;
            border-radius: 25px;
        }

    </style>

    <div class="mb-10">
        <a class="orange-button" href="{{route('supplies-page')}}">назад в поставки</a>
    </div>

{{--    toDo доделать --}}

{{--    <h4>### Добавить срок годности для каждого ингредиента. Аналитика по срокам, устраиваем акцию на продукцию с данным ингридиентом. И следим что используется.</h4>--}}
{{--    <h4>### Доделать подтягивание цен из пред. поставок</h4>--}}

    <div>

        <form class="supply-create-or-edit-form" action="" onsubmit="return false;">

            <div class="mb-10">
                <label for="">Поставщик
                    <select name="supplier">
                        @foreach($suppliers as $supplier)
                            <option @if(isset($supply) && $supply->supplier_id === $supplier->id) selected @endif value="{{$supplier->id}}">{{$supplier->title}}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="mb-10">
                <label for="">Дата
                    <input class="need-validate" name="dateSupply" type="datetime-local" value="{{isset($supply) ? date('Y-m-d\TH:i', strtotime($supply->supply_date)) : date('Y-m-d\TH:i', time())}}">
                </label>
            </div>

            <div class="mb-10">
                <label for="">Тип оплаты
                    <select name="paymentType">
                        <option value="1" @if(isset($supply) && $supply->payment_type === 1) selected @endif>Наличные</option>
                        <option value="2" @if(isset($supply) && $supply->payment_type === 2) selected @endif>Безналичные</option>
                        <option value="3" @if(isset($supply) && $supply->payment_type === 3) selected @endif>Перевод</option>
                    </select>
                </label>
            </div>

            <div class="mb-10">
                <label for="">Накладная/чек
                    <input name="file" type="file">
                </label>
            </div>

            <div class="container-for-ingredients mb-10">

            </div>

            <div class="mb-10">
                <label>Сумма
                    <input name="totalSumSupply" type="text" readonly value="0">
                </label>
            </div>

            <div class="mb-10">
                <button class="add-ingredients-in-supply-button orange-button">Добавить товар в поставку</button>
            </div>

            <div>
                <button class="save-button orange-button">Сохранить</button>
            </div>

        </form>

    </div>

@stop

@section('js')

    <script>

        let saveButton = document.body.querySelector('.save-button');
        saveButton.addEventListener('click', () => {

            let containerForIngredients = document.body.querySelector('.container-for-ingredients');

            let allIngredientsInSupply = containerForIngredients.querySelectorAll('.container-for-ingredient');

            let allIngredientsInSupplyData = [];
            let existInvalidIngredientData = false;
            allIngredientsInSupply.forEach((containerForIngredient) => {
               let ingredientId = containerForIngredient.querySelector('select[name="ingredient"]');
               let ingredientAmount = containerForIngredient.querySelector('input[name="amount"]');
               let ingredientPrice = containerForIngredient.querySelector('input[name="price"]');
               if (ingredientId.value !== 'null' && ingredientAmount.value !== '' && ingredientPrice.value !== '') {
                   containerForIngredient.classList.remove('invalid-value');
                   allIngredientsInSupplyData.push({
                       id: ingredientId.value,
                       amount: ingredientAmount.value,
                       price: ingredientPrice.value,
                   });
               } else {
                   containerForIngredient.classList.add('invalid-value');
                   existInvalidIngredientData = true;
               }
            });

            let supplierId = document.body.querySelector('select[name="supplier"]').value;
            let dateSupply = document.body.querySelector('input[name="dateSupply"]').value;
            let paymentType = document.body.querySelector('select[name="paymentType"]').value;
            let file = document.body.querySelector('input[name="file"]').files[0];

            if (file === undefined && {{empty($supply) ?: 0}}) {
                return FlashMessage('Выберите файл!');
            }

            if (dateSupply === '' || existInvalidIngredientData === true || allIngredientsInSupplyData.length === 0) {
                return FlashMessage('Заполните данные корректно!');
            }

            let data = {
                supplierId: supplierId,
                dateSupply: dateSupply,
                paymentType: paymentType,
                allIngredientsInSupplyData: JSON.stringify(allIngredientsInSupplyData),
                file: file,
                {{isset($supply) ? 'supplyId: '.$supply->id.',' : ''}}
            }

            Ajax("{{route('supply-save')}}", 'POST', data).then((response) => {
                FlashMessage(response.message);
                if (response.status === true) {
                    setTimeout(() => {
                        @if(isset($supply))
                        location.href = "{{route('supplies-page')}}";
                        @else
                        location.reload();
                        @endif
                    }, 2000)
                }
            });
        });

        let addIngredientsInSupplyButton = document.body.querySelector('.add-ingredients-in-supply-button');
        addIngredientsInSupplyButton.addEventListener('click', () => {
            AddRowIngredient();
        });

        function Search(target) {
            let options = target.parentNode.querySelectorAll('select[name="ingredient"] option');

            let regExp = new RegExp(target.value, 'ig');
            for (let i = 0; i < options.length; i++) {
                let option = options[i];

                if (option.innerHTML.match(regExp)) {
                    option.removeAttribute('hidden');
                } else {
                    option.setAttribute('hidden', 'true');
                }
            }
        }

        function AddRowIngredient(ingredientId = null, amountIngredient = null, priceIngredient = null) {
            let containerIngredients = document.body.querySelector('.container-for-ingredients');
            let rowIngredient = document.createElement('div');
            rowIngredient.className = 'container-for-ingredient flex-center-vertical m-5 border';
            rowIngredient.innerHTML =   '<div class="m-5">' +
                                            '<label for="" class="flex-column">Товар' +
                                                //'<input class="type-search" placeholder="фильтр по словам" onchange="Search(this)" type="text">' +
                                                GenerateIngredientsSelector(ingredientId) +
                                            '</label>' +
                                        '</div>' +
                                        '<div class="m-5">' +
                                            '<label for="">Количество (кг/литр)</label>' +
                                            '<input class="need-validate" name="amount" '+(ingredientId ? "value='"+amountIngredient+"'" : '' )+' type="text">' +
                                        '</div>' +
                                        '<div class="m-5">' +
                                            '<label for="">Цена за кг/литр</label>' +
                                            '<input class="need-validate" name="price" type="text" '+(ingredientId ? "value='"+priceIngredient+"'" : '' )+'>' +
                                        '</div>' +
                                        '<div class="m-5">' +
                                            '<label for="">Сумма</label>' +
                                            '<input class="need-validate" name="sum" type="text">' +
                                        '</div>' +
                                        '<div class="m-5">' +
                                            '<button class="delete-ingredient-button cp flex-center">' +
                                                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16"> <path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z"/> <path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z"/> </svg>' +
                                            '</button>' +
                                        '</div>';
            containerIngredients.append(rowIngredient);

            let ingredientSelector = rowIngredient.querySelector('select');
            SelectWithSearch(ingredientSelector);

            let deleteIngredientButton = rowIngredient.querySelector('.delete-ingredient-button')
            deleteIngredientButton.addEventListener('click', () => {
                let containerForIngredient = deleteIngredientButton.closest('.container-for-ingredient');
                containerForIngredient.remove();
            });

            let inputIngredient = rowIngredient.querySelector('select[name="ingredient"]')
            let inputIngredientAmount = rowIngredient.querySelector('input[name="amount"]');
            let inputIngredientPrice = rowIngredient.querySelector('input[name="price"]');
            let inputIngredientSum = rowIngredient.querySelector('input[name="sum"]');

            inputIngredient.addEventListener('change', (event) => {
                let ingredient = event.target.value;
                console.log(ingredient)
            });

            inputIngredientSum.addEventListener('change', () => {
                CountSumTotal();
            });

            inputIngredientAmount.addEventListener('input', () => {
                ReSum()
            });

            inputIngredientPrice.addEventListener('input', () => {
                let price = inputIngredientPrice.value.replace(/,/, '.');
                let amount = inputIngredientAmount.value.replace(/,/, '.');
                let countSum = CountSum(amount, price);
                inputIngredientSum.value = parseFloat(countSum).toFixed(2);
                CountSumTotal();
            });

            inputIngredientSum.addEventListener('input', () => {
                let amount = inputIngredientAmount.value.replace(/,/, '.');
                let sum = inputIngredientSum.value.replace(/,/, '.');
                let countSum = sum / amount;
                inputIngredientPrice.value = parseFloat(countSum).toFixed(2);
                CountSumTotal();
            });

            if(ingredientId) {
                ReSum();
            }

            function ReSum() {
                let amount = inputIngredientAmount.value.replace(/,/, '.');
                let price = inputIngredientPrice.value.replace(/,/, '.');
                let countSum = CountSum(amount, price);
                inputIngredientSum.value = parseFloat(countSum).toFixed(2);
                CountSumTotal();
            }
        }

        function CountSumTotal() {
            let allIngredientsSum = document.body.querySelectorAll('input[name="sum"]');
            let totalSumSupply = document.body.querySelector('input[name="totalSumSupply"]');
            let totalSumSupplyValue = 0;
            allIngredientsSum.forEach((sum) => {
                totalSumSupplyValue += parseFloat(sum.value);
            });
            totalSumSupply.value = parseFloat(totalSumSupplyValue).toFixed(2);
        }

        let generatedIngredientsSelector = null;
        function GenerateIngredientsSelector(ingredientId = null) {
            if (generatedIngredientsSelector === null || ingredientId !== null) {
                let tempGenerateIngredientsSelector = '<select name="ingredient">';
                tempGenerateIngredientsSelector += '<option value="null" disabled '+ (ingredientId ? '' : 'selected') +'>Выберите продукт</option>';
                Object.keys(allIngredients).forEach((key) => {
                    tempGenerateIngredientsSelector += '<option '+ (ingredientId === allIngredients[key]['id'] ? 'selected' : '') +' value="' + allIngredients[key]['id'] + '">' + allIngredients[key]['title'] + '</option>';
                });
                tempGenerateIngredientsSelector += '</select>';
                generatedIngredientsSelector = tempGenerateIngredientsSelector;
            }
            return generatedIngredientsSelector;
        }

        function CountSum(amount, price) {
            if (amount !== '' && price !== '') {
                return parseFloat(amount).toFixed(2) * parseFloat(price).toFixed(2);
            } else {
                return 0;
            }
        }

        let allIngredients = null;
        Ajax("{{route('all-ingredients')}}").then((response) => {
            allIngredients = response;
            GenerateIngredientsSelector();
            IngredientsFill();
        });

        function IngredientsFill() {
            @if(isset($ingredientsInSupply))
                @foreach($ingredientsInSupply as $ingredientInSupply)
                    AddRowIngredient({{$ingredientInSupply->ingredient_id}}, {{$ingredientInSupply->amount_ingredient}}, {{$ingredientInSupply->price_ingredient}});
                @endforeach
            @endif
        }

        function SelectWithSearch(selector) {

            selector.hide();
            let oldValue = null;

            const defaultOption = selector.querySelector('option[selected]');
            let defaultOptionText = ''
            if (defaultOption) {
                defaultOptionText = defaultOption.innerHTML;
                oldValue = defaultOption.value;
            }

            const options = selector.querySelectorAll('option');

            let container = selector.parentNode;

            let searchFieldContainer = container.querySelector('.search-field-container');
            if (!searchFieldContainer) {
                searchFieldContainer = CreateElement('div', {
                    attr: {type: 'text'},
                    class: 'search-field-container'
                }, container);
            }

            let searchField = searchFieldContainer.querySelector('.search-field');
            if (!searchField) {
                searchField = CreateElement('input', {
                    attr: {type: 'text'},
                    class: 'search-field'
                }, searchFieldContainer);
            }


            if (defaultOption.getAttribute('disabled') !== null) {
                searchField.setAttribute('placeholder', defaultOptionText)
            } else {
                searchField.value = defaultOptionText;
            }

            let customOptionsContainer = searchFieldContainer.querySelector('.custom-options-container');
            if (!customOptionsContainer) {
                customOptionsContainer = CreateElement('div', {class: 'custom-options-container hide'}, searchFieldContainer);
            }
            customOptionsContainer.innerHTML = '';

            let optionsCustom = [];
            options.forEach((option) => {
                const text = option.innerHTML;
                const value = option.value;
                if (option.getAttribute('disabled') === null) {
                    const customOption = CreateElement('div', {
                        attr: {'data-value': value},
                        class: 'custom-option',
                        content: text
                    }, customOptionsContainer);
                    optionsCustom.push(customOption);
                    customOption.addEventListener('mousedown', (event) => {
                        searchField.value = event.target.innerHTML;
                        const value = event.target.dataset.value;
                        selector.value = value;
                        oldValue = value;
                    });
                }
            });

            searchField.addEventListener('focus', (event) => {
                customOptionsContainer.show();
            });

            searchField.addEventListener('blur', (event) => {
                customOptionsContainer.hide();
                if (!oldValue) {
                    searchField.value = '';
                    for (let i = 0; i < optionsCustom.length; i++) {
                        optionsCustom[i].show();
                    }
                }
            });

            searchField.addEventListener('keyup', (event) => {
                oldValue = null;
                let target = event.target;

                let regExp = new RegExp(target.value, 'ig');
                for (let i = 0; i < optionsCustom.length; i++) {
                    let option = optionsCustom[i];

                    if (option.innerHTML.match(regExp)) {
                        option.show();
                    } else {
                        option.hide();
                    }
                }
            });
        }

    </script>

@stop
