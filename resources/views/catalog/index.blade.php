@extends('app')

@section('content')

    <style>

        input[type="radio"]:checked + label {
            background-color: #FFFFFF;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
        }
        .container-modification {
            background-color: rgb(243, 243, 247);
            padding: 5px;
            display: flex;
            justify-content: space-between;
            border-radius: 100px;
        }
        .modification-button {
            padding: 5px 10px;
            border-radius: 100px;
            cursor: pointer;
            transition: background-color 100ms;

        }

        .container-button-put-in-basket {

        }

        .button-put-in-basket {

        }

    </style>

    <div class="flex">

        @foreach($allProducts as $product)

            <div class="button-open-product border p-10 m-10 w-10 flex-column cp" data-product-id="{{$product->id}}">

                <div class="w-100 h-100">
                    <img src="{{url('img-pizza.jpeg')}}" class="w-100" alt="">
                </div>

                <div class="border p-10">{{$product->title}}</div>

                <div class="flex">

                    <div class="border p-10 w-50">от {{$product->MinimumPrice()}} ₽</div>

                    <div class="w-50">
                        <button class="w-100 h-100">в корзину</button>
                    </div>

                </div>

            </div>

        @endforeach

    </div>

@stop

@section('js')

    <script>

        document.body.querySelectorAll('.button-open-product').forEach((el) => {
            el.addEventListener('click', () => {
                let productId = el.dataset.productId;
                ProductWindowGenerator(productId);
            });
        });

        function ProductWindowGenerator(productId) {

            let productTitle = allProducts['product-'+productId].title;
            ModificationsGenerate(productId);

            let productWindow = document.createElement('div');
            productWindow.className = 'product-window w-100 h-100 pos-fix';
            productWindow.innerHTML =
                '<div class="product-window-shadow w-100 h-100">' +
                '</div>' +
                '<div class="pos-abs bg-white border-radius-10 scroll-off" style="top: 50px;left: calc(50% - 20%); width: 40%">' +
                    '<div class="button-close-product-window pos-abs flex cp" style="right: 10px; top: 10px">'+SvgCloseButton+'</div>' +
                    '<div class="flex">' +
                        '<div class="w-50 m-25">' +
                            '<div>' +
                                '<img src="http://pizza.local/img-pizza.jpeg" class="w-100" alt="">' +
                            '</div>' +
                            '<div>традиционное итальянское блюдо в виде тонкой круглой лепёшки (пирога) из дрожжевого теста, выпекаемой с уложенной сверху начинкой из томатного соуса, кусочков сыра, мяса, овощей, грибов и других продуктов.</div>' +
                        '</div>' +
                        '<div class="w-50 flex-column p-25" style="background: rgb(252, 252, 252);">' +
                            '<div class="text-center">'+productTitle+'</div>' +
                            IngredientsGenerator(productId) +
                            // '<div>Ингредиенты</div>' +
                            ModificationsGenerate(productId) +
                            // '<div class="container-modification">' +
                            //     '<div>' +
                            //         '<input name="mod" class="hide" id="1" type="radio" checked/>' +
                            //         '<label class="modification-button" for="1">Модификатор 1</label>' +
                            //     '</div>' +
                            //     '<div>' +
                            //         '<input name="mod" class="hide" id="2" type="radio"/>' +
                            //         '<label class="modification-button" for="2">Модификатор 2</label>' +
                            //     '</div>' +
                            //     '<div>' +
                            //         '<input name="mod" class="hide" id="3" type="radio"/>' +
                            //         '<label class="modification-button" for="3">Модификатор 3</label>' +
                            //     '</div>' +
                            // '</div>' +
                            '<div class="container-button-put-in-basket mt-a mx-a w-75"><button class="button-put-in-basket w-100 p-5 cp">в корзину</button></div>' +
                        '</div>' +
                    '</div>' +
                '</div>';

            let productWindowShadow = productWindow.querySelector('.product-window-shadow');
            productWindowShadow.addEventListener('click', () => {
                productWindow.remove();
            });

            let buttonCloseProductWindowShadow = productWindow.querySelector('.button-close-product-window');
            buttonCloseProductWindowShadow.addEventListener('click', () => {
                productWindow.remove();
            });

            let buttonPutInBasket = productWindow.querySelector('.button-put-in-basket');
            buttonPutInBasket.innerHTML = 'Добавить в корзину за ' + startPriceModification + ' ₽';

            productWindow.querySelectorAll('.modification-button').forEach((el) => {
                el.addEventListener('click', () => {
                   let priceModification = el.dataset.priceModification;
                    buttonPutInBasket.innerHTML = 'Добавить в корзину за ' + priceModification + ' ₽';
                });
            });

            buttonPutInBasket.addEventListener('click', () => {
                FlashMessage('На эту кнопку товар добавляется в корзину');
            });

            document.body.prepend(productWindow);
        }

        let startPriceModification = 0;
        function ModificationsGenerate(productId) {
            let containerAllModifications = '<div>';
            Object.keys(allProducts['product-'+productId]['modifications']).forEach(function (modificationTypeId) {
                let modificationType = allProducts['product-'+productId]['modifications'][modificationTypeId];
                let modificationTypeHTML = '<div class="container-modification">';
                let i = 0;
                Object.keys(modificationType).forEach(function (modificationId) {
                    let modification = modificationType[modificationId];
                    let checkedInput = i === 0 ? 'checked' : '';
                    i === 0 ? startPriceModification = modification.sellingPrice : '';
                    let buttonWidth = 'width:' + (100 / modification.modificationTypeCount) + '%;';
                    let modificationIdHTML =
                        '<div class="text-center" style="' + buttonWidth + '">' +
                            '<input name="' + modificationTypeId + '" class="hide" id="' + modificationId + '" type="radio" ' + checkedInput + '/>' +
                            '<label class="modification-button" data-price-modification="' + modification.sellingPrice + '" for="' + modificationId + '">' + modification.title + ' - ' + modification.value + '</label>' +
                        '</div>';
                    modificationTypeHTML += modificationIdHTML;
                    i++;
                });
                modificationTypeHTML += '</div>';
                containerAllModifications += modificationTypeHTML;
            });
            containerAllModifications += '</div>';
            return containerAllModifications;
        }

        function IngredientsGenerator(productId) {
            let containerAllModifications = '<div class="flex-wrap">';
            Object.keys(allProducts['product-'+productId]['modifications']).forEach(function (modificationTypeId) {
                let modificationType = allProducts['product-'+productId]['modifications'][modificationTypeId];
                let i = 0;
                Object.keys(modificationType).forEach(function (modificationId) {
                    let modification = modificationType[modificationId];
                    let ingredients = modification.ingredients;
                    Object.keys(ingredients).forEach(function (ingredientId) {
                        let ingredient = ingredients[ingredientId];
                        if (i === 0) {
                            containerAllModifications += '<div class="p-10 flex-center"><input checked type="checkbox" id="' + ingredientId + '"><label for="' + ingredientId + '">' + ingredient.title + '</label></div>';
                        }
                    });
                    i++;
                });
            });
            containerAllModifications += '</div>';
            return containerAllModifications;
        }

        let allProducts = [];
        @foreach($allProducts as $product)  {{-- крутим продукты --}}

            allProducts['product-{{$product->id}}'] = {
                title: '{{$product->title}}',
                modifications: [],
            };

            @php
            // массив для однотипных модификаций
            $arrModifications = [];
            @endphp

            @foreach($product->Modifications as $modification)  {{-- крутим модификации --}}

                @if(!in_array($modification->Modification->type_id, $arrModifications))   {{-- если еще не добавляли данную модификацию --}}
                    @php
                        $arrModifications[] = $modification->Modification->type_id;  // добавляем тип модификации который еще не добавляли
                    @endphp
                    allProducts['product-{{$product->id}}']['modifications']['modification-type-{{$modification->Modification->type_id}}'] = [];   {{-- создаем под тип модификации массив --}}
                @endif

                allProducts['product-{{$product->id}}']['modifications']['modification-type-{{$modification->Modification->type_id}}']['modification-{{$modification->id}}'] = {
                    id: '{{$modification->id}}',
                    title: '{{$modification->Modification->title}}',
                    value: '{{$modification->Modification->value}}',
                    sellingPrice: '{{$modification->selling_price}}',
                    modificationTypeCount: '{{sizeof($product->Modifications)}}',
                    ingredients: [],
                };

                @foreach($modification->Ingredients as $ingredient)  {{-- крутим ингредиенты --}}

                    allProducts['product-{{$product->id}}']['modifications']['modification-type-{{$modification->Modification->type_id}}']['modification-{{$modification->id}}']['ingredients']['ingredient-{{$ingredient->Ingredient->id}}'] = {
                        id: '{{$ingredient->Ingredient->id}}',
                        title: '{{$ingredient->Ingredient->title}}',
                    }

                @endforeach

            @endforeach

        @endforeach

    </script>

@stop
