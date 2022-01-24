@extends('app')

@section('content')

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

    <div>

        <form class="product-create-or-edit-form" action="" onsubmit="return false;">

            <div class="mb-10">
                <label for="">Название
                    <input class="need-validate" name="title" type="text">
                </label>
            </div>

            <div class="mb-10">
                <div>Модификации</div>
                <div class="modifications-containers">

                </div>

                <div>
                    <button class="add-modification-button orange-button">Добавить модификацию</button>
                </div>
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
            saveButton.hide();
            let data = GetDataFormContainer('product-create-or-edit-form');

            if (!CheckingFieldForEmptiness('product-create-or-edit-form', true)) {
                return;
            }

            Ajax("{{route('product-create')}}", 'POST', data).then((response) => {
                FlashMessage(response.message);
                if (response.status === false) {
                    saveButton.show();
                }
            })
        });

        let modificationsContainers = document.body.querySelector('.modifications-containers');
        let addModificationButton = document.body.querySelector('.add-modification-button');
        let tempModificationId = 0;
        addModificationButton.addEventListener('click', () => {
            modificationsContainers.append(GenerateModificationContainer())
            tempModificationId++;
        });

        function GenerateModificationContainer() {
            let tempId = tempModificationId;
            let generatedModificationContainer = document.createElement('div');
            generatedModificationContainer.className = 'modification-container border m-5 p-5';
            generatedModificationContainer.innerHTML =  '' +
                                                            GenerateModificationSelector(tempId)+
                                                            '<div class="ingredients-container">' +
                                                            '</div>' +
                                                            '<div>' +
                                                                '<button class="add-ingredient-button orange-button mt-10">Добавить ингредиенты</button>' +
                                                            '</div>' +
                                                            '<div class="pricing-container mb-10">' +
                                                                '<div>' +
                                                                    '<label>Себестоимость</label>' +
                                                                    '<input class="cost-price" readonly type="text">' +
                                                                '</div>' +
                                                                '<div>' +
                                                                    '<label>Наценка</label>' +
                                                                    '<input class="markup" type="text">' +
                                                                '</div>' +
                                                                '<div>' +
                                                                    '<label>Цена продажи</label>' +
                                                                    '<input class="need-validate selling-price" name="modifications['+ tempId +'][price]" type="text" value="">' +
                                                                '</div>' +
                                                            '</div>' +
                                                            '<div>' +
                                                                '<button class="delete-modification-button orange-button">Удалить модификацию</button>' +
                                                            '</div>';
            let ingredientsContainer = generatedModificationContainer.querySelector('.ingredients-container');
            let addIngredientButton = generatedModificationContainer.querySelector('.add-ingredient-button');
            let deleteModificationButton = generatedModificationContainer.querySelector('.delete-modification-button');
            let costPriceInput = generatedModificationContainer.querySelector('.cost-price');
            let markupInput = generatedModificationContainer.querySelector('.markup');
            let sellingPriceInput = generatedModificationContainer.querySelector('.selling-price');
            addIngredientButton.addEventListener('click', () => {
                ingredientsContainer.append(GenerateIngredientContainer(tempId))
                let allInputIngredientAmount = ingredientsContainer.querySelectorAll('.ingredient-amount');
                let allInputIngredientPrice = ingredientsContainer.querySelectorAll('.ingredient-price');
                let selectorsIngredients = ingredientsContainer.querySelectorAll('.selector-ingredients');

                allInputIngredientAmount.forEach((el) => {
                   el.addEventListener('input', () => {
                       let costPriceValue = 0.00;
                       allInputIngredientPrice.forEach((el) => {
                           costPriceValue = parseFloat(costPriceValue) + parseFloat(el.value);
                       });
                       costPriceInput.value = parseFloat(costPriceValue).toFixed(2);
                       markupInput.value = ((sellingPriceInput.value / costPriceInput.value) - 1) * 100;
                   });
                });

                let deleteIngredientButtons = ingredientsContainer.querySelectorAll('.delete-ingredient-button');
                deleteIngredientButtons.forEach((el) => {
                    el.addEventListener('click', () => {
                        let allInputIngredientPrice = ingredientsContainer.querySelectorAll('.ingredient-price');
                        let costPriceValue = 0.00;
                        allInputIngredientPrice.forEach((el) => {
                            costPriceValue = parseFloat(costPriceValue) + parseFloat(el.value);
                        });
                        costPriceInput.value = parseFloat(costPriceValue).toFixed(2);
                        markupInput.value = ((sellingPriceInput.value / costPriceInput.value) - 1) * 100;
                    });
                });

                selectorsIngredients.forEach((el) => {
                    el.addEventListener('change', () => {
                        let allInputIngredientPrice = ingredientsContainer.querySelectorAll('.ingredient-price');
                        let costPriceValue = 0.00;
                        allInputIngredientPrice.forEach((el) => {
                            costPriceValue = parseFloat(costPriceValue) + parseFloat(el.value);
                        });
                        costPriceInput.value = parseFloat(costPriceValue).toFixed(2);
                        markupInput.value = ((sellingPriceInput.value / costPriceInput.value) - 1) * 100;
                    });
                });

            });

            deleteModificationButton.addEventListener('click', () => {
                generatedModificationContainer.remove()
            });

            markupInput.addEventListener('input', (event) => {
                let markupValue = event.target.value;
                sellingPriceInput.value = costPriceInput.value * ((markupValue / 100) + 1);
            });

            sellingPriceInput.addEventListener('input', (event) => {
                let sellingPriceValue = event.target.value;
                markupInput.value = ((sellingPriceValue / costPriceInput.value) - 1) * 100;
            });

            return generatedModificationContainer;
        }

        let ingredientsTempId = -1;
        function GenerateIngredientContainer(tempId) {
            ingredientsTempId++;
            let generatedIngredientContainer = document.createElement('div');
            generatedIngredientContainer.className = 'ingredient-container border m-5 p-5 flex-wrap';
            generatedIngredientContainer.innerHTML =    '' +
                                                            GenerateIngredientsSelector(tempId)+
                                                            '<div class="m-5">' +
                                                                '<label>Количество</label>' +
                                                                '<input class="need-validate ingredient-amount" name="modifications['+ tempId +'][ingredients][amount][]" type="text">' +
                                                            '</div>' +
                                                            '<div class="m-5">' +
                                                                '<label>Стоимость за единицу</label>' +
                                                                '<input class="unit-ingredient-price" type="text" readonly>' +
                                                            '</div>' +
                                                            '<div class="m-5">' +
                                                                '<label>Стоимость</label>' +
                                                                '<input class="ingredient-price" type="text" readonly>' +
                                                            '</div>' +
                                                            '<div class="flex-column-center">' +
                                                                '<div>Видимый</div>'+
                                                                '<label class="custom-checkbox-label" for="ingredient-'+ ingredientsTempId +'">'+
                                                                    '<input class="visible" type="checkbox" checked id="ingredient-'+ ingredientsTempId +'" name="modifications['+ tempId +'][ingredients][visible][]"/>'+
                                                                    '<div class="custom-checkbox-slider round"></div>'+
                                                                '</label>'+
                                                            '</div>' +
                                                            '<div class="m-5">' +
                                                                '<button class="delete-ingredient-button flex-center cp">' +
                                                                    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16"> <path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z"/> <path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z"/> </svg>' +
                                                                '</button>' +
                                                            '</div>';

            let ingredientPriceInput = generatedIngredientContainer.querySelector('.ingredient-price');
            let unitIngredientPrice = generatedIngredientContainer.querySelector('.unit-ingredient-price');

            let deleteIngredientButton = generatedIngredientContainer.querySelector('.delete-ingredient-button');
            deleteIngredientButton.addEventListener('click', () => {
                generatedIngredientContainer.remove();
            });

            let ingredientAmountInput = generatedIngredientContainer.querySelector('.ingredient-amount');
            ingredientAmountInput.addEventListener('input', (event) => {
                let selectedIndex = event.target.closest('.ingredient-container').querySelector('.selector-ingredients').options.selectedIndex;
                let lastPriceIngredient = event.target.closest('.ingredient-container').querySelector('.selector-ingredients').options[selectedIndex].dataset.lastPriceIngredient;
                if (lastPriceIngredient !== undefined) {
                    let ingredientAmountValue = event.target.value;
                    ingredientPriceInput.value = parseFloat(ingredientAmountValue * lastPriceIngredient).toFixed(2);
                }
            });

            let selectorIngredients = generatedIngredientContainer.querySelector('.selector-ingredients');
            selectorIngredients.addEventListener('change', (event) => {
                let selectedIndex = event.target.options.selectedIndex;
                let lastPriceIngredient = event.target.options[selectedIndex].dataset.lastPriceIngredient;
                unitIngredientPrice.value = lastPriceIngredient;
                if (lastPriceIngredient !== undefined) {
                    let ingredientAmountValue = ingredientAmountInput.value === '' ? 1 : ingredientAmountInput.value;
                    ingredientPriceInput.value = parseFloat(ingredientAmountValue * lastPriceIngredient).toFixed(2);
                }
            });
            return generatedIngredientContainer;
        }

        let generatedIngredientsSelector = null;
        function GenerateIngredientsSelector(tempId) {
            //if (generatedIngredientsSelector === null) {
                let tempGenerateIngredientsSelector =   '<div><label>Ингредиент</label><select class="selector-ingredients need-validate" name="modifications['+ tempId +'][ingredients][id][]">';

                tempGenerateIngredientsSelector += '<option value="" disabled selected>Выберите ингредиент</option>';
                Object.keys(allIngredients).forEach((key) => {
                    tempGenerateIngredientsSelector += '<option data-last-price-ingredient="' + allIngredients[key]['last_price_ingredient'] + '" value="' + allIngredients[key]['id'] + '">' + allIngredients[key]['title'] + '</option>';
                });
                tempGenerateIngredientsSelector += '</select></div>';
                generatedIngredientsSelector = tempGenerateIngredientsSelector;
            //}
            return generatedIngredientsSelector;
        }

        let generatedModificationsSelector = null;
        function GenerateModificationSelector(tempId) {
            //if (generatedModificationsSelector === null) {
                let tempGeneratedModificationsSelector = '<select class="need-validate" name="modifications['+ tempId +'][id]">';
                tempGeneratedModificationsSelector += '<option value="" disabled selected>Выберите модификацию</option>';
                Object.keys(allModifications).forEach((key) => {
                    tempGeneratedModificationsSelector += '<option value="' + allModifications[key]['id'] + '">' + allModifications[key]['title'] + ' - ' + allModifications[key]['value'] + '</option>';
                });
                tempGeneratedModificationsSelector += '</select>';
                generatedModificationsSelector = tempGeneratedModificationsSelector;
            //}
            return generatedModificationsSelector;
        }

        let allModifications = null;
        Ajax("{{route('all-modifications')}}").then((response) => {
            allModifications = response.result;
            GenerateModificationContainer();
        });

        let allIngredients = null;
        Ajax("{{route('all-ingredients')}}").then((response) => {
            allIngredients = response;
            GenerateIngredientsSelector();
        });


    </script>

@stop
