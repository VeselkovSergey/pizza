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

    <div class="mb-10">
        <a class="orange-button" href="{{route('write-offs-page')}}">назад в списания</a>
    </div>

    <div>

        <div>

            <div class="mb-10">
                <label for="">Дата
                    <input class="need-validate" name="writeOffDate" type="datetime-local" value="{{isset($writeOff) ? date('Y-m-d\TH:i', strtotime($writeOff->date)) : date('Y-m-d\TH:i', time())}}">
                </label>
            </div>

            <div class="mb-10">
                <label>
                    <div>Описание (причина)</div>
                    <textarea  class="w-100" name="description" rows="5">{{isset($writeOff) ? $writeOff->description : ''}}</textarea>
                </label>
            </div>

            <div class="ingredients-containers mb-10">

            </div>

            <div class="mb-10">
                <button class="add-ingredients-button orange-button">Добавить товар в списание</button>
            </div>

            <div>
                <button class="save-button orange-button">Сохранить</button>
            </div>

        </div>

    </div>

@stop

@section('js')

    <script>

        let saveButton = document.body.querySelector('.save-button');
        saveButton.addEventListener('click', () => {

            let containerForIngredients = document.body.querySelector('.ingredients-containers');

            let ingredientsContainers = containerForIngredients.querySelectorAll('.ingredient-container');

            let allIngredientsData = [];
            let existInvalidIngredientData = false;
            ingredientsContainers.forEach((ingredientContainer) => {
               let ingredientId = ingredientContainer.querySelector('select[name="ingredient"]');
               let ingredientAmount = ingredientContainer.querySelector('input[name="amount"]');
               if (ingredientId.value !== null && ingredientId.value !== 'null' && ingredientAmount.value !== '') {
                   ingredientContainer.classList.remove('invalid-value');
                   allIngredientsData.push({
                       id: ingredientId.value,
                       amount: ingredientAmount.value,
                   });
               } else {
                   ingredientContainer.classList.add('invalid-value');
                   existInvalidIngredientData = true;
               }
            });

            let writeOffDate = document.body.querySelector('input[name="writeOffDate"]').value;
            let writeOffDescription = document.body.querySelector('textarea[name="description"]');

            writeOffDescription.value === '' ? writeOffDescription.classList.add('invalid-value') : writeOffDescription.classList.remove('invalid-value');

            if (writeOffDescription.value === '' || writeOffDate === '' || existInvalidIngredientData === true || allIngredientsData.length === 0) {
                return FlashMessage('Заполните данные корректно!');
            }

            let data = {
                date: writeOffDate,
                description: writeOffDescription.value,
                allIngredientsData: JSON.stringify(allIngredientsData),
                {{isset($writeOff) ? 'writeOffId: '.$writeOff->id.',' : ''}}
            }

            Ajax("{{route('write-off-save')}}", 'POST', data, true).then((response) => {
                FlashMessage(response.message);
                if (response.status === true) {
                    setTimeout(() => {
                        @if(isset($writeOff))
                        location.href = "{{route('write-offs-page')}}";
                        @else
                        location.reload();
                        @endif
                    }, 2000)
                }
            });
        });

        let addIngredientsButton = document.body.querySelector('.add-ingredients-button');
        addIngredientsButton.addEventListener('click', () => {
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

        function AddRowIngredient(ingredientId = null, amountIngredient = null) {
            let ingredientsContainers = document.body.querySelector('.ingredients-containers');
            let rowIngredient = CreateElement('div', {class: 'ingredient-container flex-center-vertical mb-5 border-grey border-radius-5'});
            rowIngredient.innerHTML =   '<div class="m-5">' +
                                            '<label for="" class="flex-column">Товар' +
                                                GenerateIngredientsSelector(ingredientId) +
                                            '</label>' +
                                        '</div>' +
                                        '<div class="m-5">' +
                                            '<label for="">Количество (кг/литр)</label>' +
                                            '<input class="need-validate" name="amount" value="'+(ingredientId ? amountIngredient : 1 )+'" type="text">' +
                                        '</div>' +
                                        '<div class="m-5">' +
                                            '<button class="delete-ingredient-button cp flex-center">' +
                                                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16"> <path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z"/> <path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z"/> </svg>' +
                                            '</button>' +
                                        '</div>';
            ingredientsContainers.append(rowIngredient);

            let ingredientSelector = rowIngredient.querySelector('select');
            SelectWithSearch(ingredientSelector);

            let deleteIngredientButton = rowIngredient.querySelector('.delete-ingredient-button')
            deleteIngredientButton.addEventListener('click', () => {
                let ingredientContainer = deleteIngredientButton.closest('.ingredient-container');
                ingredientContainer.remove();
            });
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

        let allIngredients = null;
        Ajax("{{route('all-ingredients')}}").then((response) => {
            allIngredients = response;
            GenerateIngredientsSelector();
            IngredientsFill();
        });

        function IngredientsFill() {
            @if(isset($ingredientsInWriteOff))
                @foreach($ingredientsInWriteOff as $ingredient)
                    AddRowIngredient({{$ingredient->ingredient_id}}, {{$ingredient->amount_ingredient}});
                @endforeach
            @endif
        }

    </script>

@stop
