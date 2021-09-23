@extends('app')

@section('content')

    <div>

        <form class="product-create-or-edit-form" action="" onsubmit="return false;">

            <div>
                <label for="">Название</label>
                <input class="need-validate" name="title" type="text">
            </div>

            <div>
                <div>Модификации</div>
                <div class="modifications-containers">

                </div>

                <div>
                    <button class="add-modification-button">Добавить модификацию</button>
                </div>
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

            if (!CheckingFieldForEmptiness('product-create-or-edit-form', true)) {
                return;
            }

            Ajax("{{route('product-save')}}", 'POST', {title: title}).then((response) => {
                FlashMessage(response.message);
            })
        });

        let modificationsContainers = document.body.querySelector('.modifications-containers');
        let addModificationButton = document.body.querySelector('.add-modification-button');
        addModificationButton.addEventListener('click', () => {
            modificationsContainers.append(GenerateModificationContainer())
        });

        function GenerateModificationContainer() {
                let generatedModificationContainer = document.createElement('div');
                generatedModificationContainer.classList.add('modification-container');
                generatedModificationContainer.classList.add('border');
                generatedModificationContainer.classList.add('m-5');
                generatedModificationContainer.classList.add('p-5');
                generatedModificationContainer.innerHTML =  '' +
                                                                    GenerateModificationSelector()+
                                                                    '<div class="ingredients-container">' +
                                                                    '</div>' +
                                                                    '<div>' +
                                                                        '<button class="add-ingredient-button">Добавить ингредиенты</button>' +
                                                                    '</div>' +
                                                                    '<div class="pricing-container">' +
                                                                        '<div>' +
                                                                            '<label>Себестоимость</label>' +
                                                                            '<input readonly type="text">' +
                                                                        '</div>' +
                                                                        '<div>' +
                                                                            '<label>Наценка</label>' +
                                                                            '<input type="text">' +
                                                                        '</div>' +
                                                                        '<div>' +
                                                                            '<label>Цена продажи</label>' +
                                                                            '<input type="text">' +
                                                                        '</div>' +
                                                                    '</div>' +
                                                                    '<div>' +
                                                                        '<button class="delete-modification-button">Удалить модификацию</button>' +
                                                                    '</div>';
                let ingredientsContainer = generatedModificationContainer.querySelector('.ingredients-container');
                let addIngredientButton = generatedModificationContainer.querySelector('.add-ingredient-button');
                let deleteModificationButton = generatedModificationContainer.querySelector('.delete-modification-button');
                addIngredientButton.addEventListener('click', () => {
                    ingredientsContainer.append(GenerateIngredientContainer())
                });
            deleteModificationButton.addEventListener('click', () => {
                generatedModificationContainer.remove()
            });
            //}
            return generatedModificationContainer;
        }



        function GenerateIngredientContainer() {
            let generatedIngredientContainer = document.createElement('div');
            generatedIngredientContainer.classList.add('ingredient-container');
            generatedIngredientContainer.classList.add('border');
            generatedIngredientContainer.classList.add('m-5');
            generatedIngredientContainer.classList.add('p-5');
            generatedIngredientContainer.classList.add('flex');
            generatedIngredientContainer.innerHTML =    '' +
                                                            GenerateIngredientsSelector()+
                                                            '<div>' +
                                                                '<label>Количество</label>' +
                                                                '<input type="text">' +
                                                            '</div>' +
                                                            '<div>' +
                                                                '<button class="delete-ingredient-button">Удалить ингредиент</button>' +
                                                            '</div>';
            let deleteIngredientButton = generatedIngredientContainer.querySelector('.delete-ingredient-button');
            deleteIngredientButton.addEventListener('click', () => {
                generatedIngredientContainer.remove();
            });
            return generatedIngredientContainer;
        }

        let generatedIngredientsSelector = null;
        function GenerateIngredientsSelector() {
            if (generatedIngredientsSelector === null) {
                let tempGenerateIngredientsSelector = '<select name="ingredient">';
                tempGenerateIngredientsSelector += '<option value="null" disabled selected>Выберите ингредиент</option>';
                Object.keys(allIngredients).forEach((key) => {
                    tempGenerateIngredientsSelector += '<option value="' + allIngredients[key]['id'] + '">' + allIngredients[key]['title'] + '</option>';
                });
                tempGenerateIngredientsSelector += '</select>';
                generatedIngredientsSelector = tempGenerateIngredientsSelector;
            }
            return generatedIngredientsSelector;
        }

        let generatedModificationsSelector = null;
        function GenerateModificationSelector() {
            if (generatedModificationsSelector === null) {
                let tempGeneratedModificationsSelector = '<select name="modification">';
                tempGeneratedModificationsSelector += '<option value="null" disabled selected>Выберите модификацию</option>';
                Object.keys(allModifications).forEach((key) => {
                    tempGeneratedModificationsSelector += '<option value="' + allModifications[key]['id'] + '">' + allModifications[key]['title'] + ' - ' + allModifications[key]['value'] + '</option>';
                });
                tempGeneratedModificationsSelector += '</select>';
                generatedModificationsSelector = tempGeneratedModificationsSelector;
            }
            return generatedModificationsSelector;
        }



        let allModifications = null;
        Ajax("{{route('all-modifications')}}").then((response) => {
            allModifications = response.result;
            GenerateModificationContainer();
        });

        let allIngredients = null;
        Ajax("{{route('all-ingredients')}}").then((response) => {
            allIngredients = response.result;
            GenerateIngredientsSelector();
        });


    </script>

@stop
