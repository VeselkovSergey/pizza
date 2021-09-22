@extends('app')

@section('content')

    <h4>### Добавить срок годности для каждого ингредиента. Аналитика по срокам, устраиваем акцию на продукцию с данным ингридиентом. И следим что используется.</h4>

    <div>

        <form class="supply-create-or-edit-form" action="" onsubmit="return false;">

            <div>
                <label for="">Поставщик</label>
                <select name="supplier">
                    @foreach($suppliers as $supplier)
                        <option value="{{$supplier->id}}">{{$supplier->title}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="">Дата</label>
                <input class="need-validate" name="dateSupply" type="datetime-local" value="{{date('Y-m-d\TH:i', time())}}">
            </div>
            <div>
                <label for="">Тип оплаты</label>
                <select name="paymentType">
                    <option value="1">Наличные</option>
                    <option value="2">Безналичные</option>
                    <option value="3">Перевод</option>
                </select>
            </div>

            <div class="container-for-ingredients">

            </div>

            <div>
                <button class="add-ingredients-in-supply-button">Добавить товар в поставку</button>
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

            if (dateSupply === '' || existInvalidIngredientData === true || allIngredientsInSupplyData.length === 0) {
                return FlashMessage('Заполните данные корректно!');
            }

            let data = {
                supplierId: supplierId,
                dateSupply: dateSupply,
                paymentType: paymentType,
                allIngredientsInSupplyData: JSON.stringify(allIngredientsInSupplyData),
            }

            Ajax("{{route('supply-save')}}", 'POST', data).then((response) => {
                FlashMessage(response.message);
            });
        });

        let addIngredientsInSupplyButton = document.body.querySelector('.add-ingredients-in-supply-button');
        addIngredientsInSupplyButton.addEventListener('click', () => {
            AddRowIngredient();
        });

        function AddRowIngredient() {
            let containerIngredients = document.body.querySelector('.container-for-ingredients');
            let rowIngredient = document.createElement('div');
            rowIngredient.classList.add('container-for-ingredient');
            rowIngredient.classList.add('flex');
            rowIngredient.classList.add('m-5');
            rowIngredient.classList.add('border');
            rowIngredient.innerHTML =   '<div class="m-5">' +
                                            '<label for="">Товар</label>' +
                                            GeneratedInputIngredients() +
                                        '</div>' +
                                        '<div class="m-5">' +
                                            '<label for="">Количество</label>' +
                                            '<input class="need-validate" name="amount" type="text">' +
                                        '</div>' +
                                        '<div class="m-5">' +
                                            '<label for="">Цена</label>' +
                                            '<input class="need-validate" name="price" type="text">' +
                                        '</div>' +
                                        '<div class="m-5">' +
                                            '<label for="">Сумма</label>' +
                                            '<input class="need-validate" name="sum" type="text" readonly>' +
                                        '</div>' +
                                        '<div class="m-5">' +
                                            '<button class="delete-ingredient-button">Удалить</button>' +
                                        '</div>';
            containerIngredients.append(rowIngredient);

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

            inputIngredientAmount.addEventListener('input', (event) => {
                let amount = inputIngredientAmount.value;
                let price = inputIngredientPrice.value;
                let countSum = CountSum(amount, price);
                inputIngredientSum.value = countSum;
            });

            inputIngredientPrice.addEventListener('input', (event) => {
                let price = inputIngredientPrice.value;
                let amount = inputIngredientAmount.value;
                let countSum = CountSum(amount, price);
                inputIngredientSum.value = countSum;
            });
        }

        let generatedInputIngredients = null;

        function GeneratedInputIngredients() {
            if (generatedInputIngredients === null) {
                let tempGeneratedInputIngredients = '<select name="ingredient">';
                tempGeneratedInputIngredients += '<option value="null" disabled selected>Выберите продукт</option>';
                Object.keys(allIngredients).forEach((key) => {
                    tempGeneratedInputIngredients += '<option value="' + allIngredients[key]['id'] + '">' + allIngredients[key]['title'] + '</option>';
                });
                tempGeneratedInputIngredients += '</select>';
                generatedInputIngredients = tempGeneratedInputIngredients;
            }
            return generatedInputIngredients;

        }


        function CountSum(amount, price) {
            if (amount !== '' && price !== '') {
                return amount * price;
            } else {
                return 0;
            }
        }

        let allIngredients = null;
        Ajax("{{route('all-ingredients')}}").then((response) => {
            allIngredients = response.result;
            GeneratedInputIngredients();
        });

    </script>

@stop
