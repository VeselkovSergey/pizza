@extends('app')

@section('content')

    <style>

        input[type="radio"]:checked.modification-input + label {
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

    <div class="flex-wrap">

        @foreach($allProducts as $product)

            <div class="button-open-product {{--p-10 m-10--}} w-10 w-100 flex-column cp" data-product-id="{{$product->id}}">

                <div class="m-10 border flex-column">
                    <div class="w-100 h-100">
                        <img src="{{url('img-pizza.jpeg')}}" class="w-100" alt="">
                    </div>

                    <div class="border p-10">{{$product->title}}</div>

                    <div class="flex">

                        <div class="border p-10 w-50">от {{$product->minimumPrice}} ₽</div>

                        <div class="w-50">
                            <button class="w-100 h-100">в корзину</button>
                        </div>

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

        let modificationSelected = null;
        function ProductWindowGenerator(productId) {

            let productTitle = allProducts['product-'+productId].title;

            let imgUrl = "{{url('img-pizza.jpeg')}}";

            let productWindow = document.createElement('div');
            productWindow.className = 'product-window w-100 h-100 pos-fix z-2';
            productWindow.innerHTML =
                '<div class="modal-window pos-abs scroll-off flex-center">' +
                    '<div class="product-window-shadow w-100 h-100"></div>' +
                    '<div class="modal-window-content pos-rel bg-white bg-white border-radius-10">' +
                        '<div class="button-close-product-window pos-abs flex cp" style="right: 20px; top: 20px">'+SvgCloseButton+'</div>' +
                        '<div class="container-content-in-modal-window scroll-auto">' +
                            '<div class="container-product p-25 flex">' +
                                '<div class="container-img-and-about-product w-50">' +
                                    '<div class="w-100">' +
                                        '<div>' +
                                            '<img src="' + imgUrl + '" class="w-100" alt="">' +
                                        '</div>' +
                                        '<p>Традиционное итальянское блюдо в виде тонкой круглой лепёшки (пирога) из дрожжевого теста, выпекаемой с уложенной сверху начинкой из томатного соуса, кусочков сыра, мяса, овощей, грибов и других продуктов.</p>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="container-modification-product w-50">' +
                                    '<div class="w-100 flex-column h-100" style="background: rgb(252, 252, 252);">' +
                                        '<div class="text-center">'+productTitle+'</div>' +
                                        '<div class="container-ingredients">' +
                                            IngredientsGenerator(productId) +
                                        '</div>'+
                                        ModificationsGenerate(productId) +
                                        '<div class="container-button-put-in-basket mt-a mx-a"><button class="button-put-in-basket btn first">В корзину</button></div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';

            let productWindowShadow = productWindow.querySelector('.product-window-shadow');
            productWindowShadow.addEventListener('click', () => {
                // productWindow.remove();
                productWindow.slowRemove();
            });

            let buttonCloseProductWindowShadow = productWindow.querySelector('.button-close-product-window');
            buttonCloseProductWindowShadow.addEventListener('click', () => {
                // productWindow.remove();
                productWindow.slowRemove();
            });

            let buttonPutInBasket = productWindow.querySelector('.button-put-in-basket');
            buttonPutInBasket.innerHTML = 'Добавить в корзину за ' + startSellingPriceModification + ' ₽';

            productWindow.querySelectorAll('.modification-button').forEach((el) => {
                el.addEventListener('click', () => {
                    let productId = el.dataset.productId;
                    let modificationType = el.dataset.modificationType;
                    let modificationId = el.dataset.modificationId;
                    let modification = allProducts[productId]['modifications'][modificationType][modificationId];
                    let sellingPriceModification = modification.sellingPrice;
                    let ingredients = IngredientsGenerator(null, modification);
                    let containerIngredients = productWindow.querySelector('.container-ingredients');
                    containerIngredients.innerHTML = ingredients;
                    buttonPutInBasket.innerHTML = 'Добавить в корзину за ' + sellingPriceModification + ' ₽';
                    modificationSelected = {
                        product: allProducts[productId],
                        modification: allProducts[productId]['modifications'][modificationType][modificationId],
                    }
                });
            });

            buttonPutInBasket.addEventListener('click', () => {
                FlashMessage('Добавлено: <br/>' + modificationSelected.product.title + ', ' + modificationSelected.modification.title + ' ' + modificationSelected.modification.value);
                AddProductInBasket(modificationSelected);
                // productWindow.remove();
                productWindow.slowRemove();
            });

            document.body.prepend(productWindow);
        }

        let startSellingPriceModification = 0;
        function ModificationsGenerate(productId) {
            let containerAllModifications = '<div>';
            Object.keys(allProducts['product-'+productId]['modifications']).forEach(function (modificationTypeId) {
                let modificationType = allProducts['product-'+productId]['modifications'][modificationTypeId];
                let modificationTypeHTML = '<div class="container-modification">';
                let i = 0;
                Object.keys(modificationType).forEach(function (modificationId) {
                    let modification = modificationType[modificationId];
                    let checkedInput = i === 0 ? 'checked' : '';
                    if(i === 0) {
                        startSellingPriceModification = modification.sellingPrice;
                        modificationSelected = {
                            product: allProducts['product-'+productId],
                            modification: modificationType[modificationId],
                        }
                    }
                    let buttonWidth = 'width:' + (100 / modification.modificationTypeCount) + '%;';
                    let modificationIdHTML =
                        '<div class="text-center flex" style="' + buttonWidth + '">' +
                            '<input name="' + modificationTypeId + '" class="hide modification-input" id="' + modificationId + '" type="radio" ' + checkedInput + '/>' +
                            '<label class="modification-button" data-product-id="product-' + productId + '" data-modification-type="' + modificationTypeId + '" data-modification-id="' + modificationId + '" for="' + modificationId + '">' + modification.title + ' - ' + modification.value + '</label>' +
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

        function IngredientsGenerator(productId, modification) {
            let containerAllModifications = '<div class="flex-wrap">';
            if (modification === undefined) {
                Object.keys(allProducts['product-'+productId]['modifications']).forEach(function (modificationTypeId) {
                    let modificationType = allProducts['product-'+productId]['modifications'][modificationTypeId];
                    let i = 0;
                    Object.keys(modificationType).forEach(function (modificationId) {
                        let modification = modificationType[modificationId];
                        let ingredients = modification.ingredients;
                        Object.keys(ingredients).forEach(function (ingredientId) {
                            let ingredient = ingredients[ingredientId];
                            if (i === 0) {
                                containerAllModifications += '<div class="p-5 flex-center"><input checked class="hide" type="checkbox" id="' + ingredientId + '"><label for="' + ingredientId + '">' + ingredient.title + '</label></div>';
                            }
                        });
                        i++;
                    });
                });
            } else {
                let ingredients = modification.ingredients;
                Object.keys(ingredients).forEach(function (ingredientId) {
                    let ingredient = ingredients[ingredientId];
                    containerAllModifications += '<div class="p-5 flex-center"><input checked class="hide" type="checkbox" id="' + ingredientId + '"><label for="' + ingredientId + '">' + ingredient.title + '</label></div>';
                });
            }
            containerAllModifications += '</div>';
            return containerAllModifications;
        }

        let allProducts = JSON.parse('@json($allProducts)');

    </script>

@stop
