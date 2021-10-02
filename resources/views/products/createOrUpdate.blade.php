@extends('app')

@section('content')

    <style>
        .delete-ingredient-button {
            align-self: flex-end;
        }
    </style>

    <h4>### Доделать подтягивание цен из пред. поставок</h4>

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

            //let title = document.body.querySelector('input[name="title"]').value;

            let data = GetDataFormContainer('product-create-or-edit-form');

            // if (!CheckingFieldForEmptiness('product-create-or-edit-form', true)) {
            //     return;
            // }

            console.log(data)

            Ajax("{{route('product-save')}}", 'POST', data).then((response) => {
                FlashMessage(response.message);
                if (response.status === true) {
                    location.reload();
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
                                                                    '<input class="need-validate" name="modifications['+ tempId +'][price]" type="text" value="">' +
                                                                '</div>' +
                                                            '</div>' +
                                                            '<div>' +
                                                                '<button class="delete-modification-button">Удалить модификацию</button>' +
                                                            '</div>';
            let ingredientsContainer = generatedModificationContainer.querySelector('.ingredients-container');
            let addIngredientButton = generatedModificationContainer.querySelector('.add-ingredient-button');
            let deleteModificationButton = generatedModificationContainer.querySelector('.delete-modification-button');
            addIngredientButton.addEventListener('click', () => {
                ingredientsContainer.append(GenerateIngredientContainer(tempId))
            });
            deleteModificationButton.addEventListener('click', () => {
                generatedModificationContainer.remove()
            });
            return generatedModificationContainer;
        }



        function GenerateIngredientContainer(tempId) {
            let generatedIngredientContainer = document.createElement('div');
            generatedIngredientContainer.className = 'ingredient-container border m-5 p-5 flex-wrap';
            generatedIngredientContainer.innerHTML =    '' +
                                                            GenerateIngredientsSelector(tempId)+
                                                            '<div>' +
                                                                '<label>Количество</label>' +
                                                                '<input class="need-validate" name="modifications['+ tempId +'][ingredients][amount][]" type="text">' +
                                                            '</div>' +
                                                            '<div>' +
                                                                '<label>Стоимость</label>' +
                                                                '<input type="text" readonly>' +
                                                            '</div>' +
                                                            '<div class="flex">' +
                                                                '<button class="delete-ingredient-button">Удалить ингредиент</button>' +
                                                            '</div>';
            let deleteIngredientButton = generatedIngredientContainer.querySelector('.delete-ingredient-button');
            deleteIngredientButton.addEventListener('click', () => {
                generatedIngredientContainer.remove();
            });
            return generatedIngredientContainer;
        }

        let generatedIngredientsSelector = null;
        function GenerateIngredientsSelector(tempId) {
            //if (generatedIngredientsSelector === null) {
                let tempGenerateIngredientsSelector =   '<div><label>Ингредиент</label><select class="need-validate" name="modifications['+ tempId +'][ingredients][id][]">';

                tempGenerateIngredientsSelector += '<option value="" disabled selected>Выберите ингредиент</option>';
                Object.keys(allIngredients).forEach((key) => {
                    tempGenerateIngredientsSelector += '<option value="' + allIngredients[key]['id'] + '">' + allIngredients[key]['title'] + '</option>';
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
            allIngredients = response.result;
            GenerateIngredientsSelector();
        });


    </script>

@stop
