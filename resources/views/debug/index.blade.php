@extends('app')

@section('content')

    @php($header = false)
    @php($white = true)

    <style>
        .select-with-search {
            display: none;
        }
        .search-field-container {
            width: 100%;
        }
        .search-field {
            width: 100%;
        }
        .custom-options-container {
            width: 100%;
            padding: 5px 10px;
            border: 1px solid #cbcdd1;
            border-radius: 5px;
        }
        .custom-option {
            padding: 3px;
        }
        .custom-option:hover {
            background-color: #4489c8;
        }
    </style>

    <div>
        <select class="select-with-search" name="" id="">
            <option value="Привет">Привет</option>
            <option value="Пока">Пока</option>
            <option value="Как дела?">Как дела?</option>
        </select>
    </div>

    <div>
        <select class="select-with-search" name="" id="">
            <option value="Привет">Привет1</option>
            <option value="Пока">Пока2</option>
            <option value="Как дела?">Как дела?3</option>
        </select>
    </div>

@stop

@section('js')
    <script>

        document.body.querySelectorAll('.select-with-search').forEach((selector) => {
            selector.hide();
            const options = selector.querySelectorAll('option');

            let container = selector.parentNode;

            let searchFieldContainer = container.querySelector('.search-field-container');
            if (!searchFieldContainer) {
                searchFieldContainer = CreateElement('div', {attr: {type: 'text'}, class: 'search-field-container'}, container);
            }

            let searchField = searchFieldContainer.querySelector('.search-field');
            if (!searchField) {
                searchField = CreateElement('input', {attr: {type: 'text'}, class: 'search-field'}, searchFieldContainer);
            }

            let customOptionsContainer = searchFieldContainer.querySelector('.custom-options-container');
            if (!customOptionsContainer) {
                customOptionsContainer = CreateElement('div', {class: 'custom-options-container hide'}, searchFieldContainer);
            }
            customOptionsContainer.innerHTML = '';

            let optionsCustom = [];
            let oldValue = null;
            options.forEach((option) => {
                const text = option.innerHTML;
                const value = option.value;
                const customOption = CreateElement('div', {attr:{'data-value': value}, class: 'custom-option', content: text}, customOptionsContainer);
                optionsCustom.push(customOption);
                customOption.addEventListener('mousedown', (event) => {
                    searchField.value = event.target.innerHTML;
                    const value = event.target.dataset.value;
                    selector.value = value;
                    oldValue = value;
                });
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
        });

    </script>
@stop
