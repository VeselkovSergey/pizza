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

        .black-t {
            background-color: black;
            color: white;
        }

    </style>

    <div class="flex-wrap">

        @foreach($allProducts as $product)

            <div class="button-open-product w-100 flex-column cp" data-product-id="{{$product->id}}">

                <div class="m-10 flex-column p-5 border-radius-10 shadow p-10 {{isset(request()->black) ? 'black-t' : ''}}">

                    <div class="container-product-img-and-description">
                        <div class="container-product-img mb-10">
{{--                            <img src="{{url('img-pizza.jpeg')}}" class="w-100" alt="">--}}
                            <img src="{{url((isset(request()->picture_id) ? 'pizza-blank-'.request()->picture_id : 'img-pizza' ).'.jpg')}}" class="w-100" alt="">
                        </div>

                        <div class="container-product-description p-10">
                            <div class="text-center mb-10">{{$product->title}}</div>
{{--                            <div>Описание</div>--}}
                        </div>
                    </div>

{{--                    <div class="text-center mb-10">от {{$product->minimumPrice}} ₽</div>--}}

                    <button class="w-100 h-100 bg-grey color-white border-radius-5 clear-button p-10 cp">от {{$product->minimumPrice}} ₽</button>


                </div>

            </div>


        @endforeach

    </div>

{{--    <div class="pos-fix top-0 left-0 w-100 h-100vh bg-white flex-center z-1 pre-text">--}}
{{--        <div>БРОПИЦЦА - НАСТОЯЩАЯ ПИЦЦА ДЛЯ ТЕБЯ</div>--}}
{{--    </div>--}}

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

{{--            let imgUrl = "{{url('img-pizza.jpeg')}}";--}}
            let imgUrl = "{{url('pizza-blank-1.jpg')}}";

            let productContent = document.createElement('div');
            productContent.className = 'flex-wrap product-content';
            productContent.innerHTML =
                                '<div class="container-img-and-about-product w-50">' +
                                    '<div class="w-100">' +
                                        '<div>' +
                                            '<img src="' + imgUrl + '" class="w-100" alt="">' +
                                        '</div>' +
                                        // '<p>Традиционное итальянское блюдо в виде тонкой круглой лепёшки (пирога) из дрожжевого теста, выпекаемой с уложенной сверху начинкой из томатного соуса, кусочков сыра, мяса, овощей, грибов и других продуктов.</p>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="container-modification-product w-50">' +
                                    '<div class="w-100 flex-column h-100" style="background: rgb(252, 252, 252);">' +
                                        '<div class="text-center text-up">'+productTitle+'</div>' +
                                        '<div class="container-ingredients text-down">' +
                                            IngredientsGenerator(productId) +
                                        '</div>'+
                                        ModificationsGenerate(productId) +
                                        '<div class="container-button-put-in-basket mt-a mx-a"><button class="button-put-in-basket btn first mt-25">В корзину</button></div>' +
                                    '</div>' +
                                '</div>';

            let buttonPutInBasket = productContent.querySelector('.button-put-in-basket');
            buttonPutInBasket.innerHTML = 'Добавить в корзину за ' + startSellingPriceModification + ' ₽';

            productContent.querySelectorAll('.modification-button').forEach((el) => {
                el.addEventListener('click', () => {
                    let productId = el.dataset.productId;
                    let modificationType = el.dataset.modificationType;
                    let modificationId = el.dataset.modificationId;
                    let modification = allProducts[productId]['modifications'][modificationType][modificationId];
                    let sellingPriceModification = modification.sellingPrice;
                    let ingredients = IngredientsGenerator(null, modification);
                    let containerIngredients = productContent.querySelector('.container-ingredients');
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
                modalWindow.slowRemove();
                document.body.classList.remove('scroll-off');
            });

            let modalWindow = ModalWindow(productContent);
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
                            // '<label class="modification-button"data-product-id="product-' + productId + '" data-modification-type="' + modificationTypeId + '" data-modification-id="' + modificationId + '" for="' + modificationId + '">' + modification.title + ' - ' + modification.value + '</label>' +
                            '<label class="modification-button"data-product-id="product-' + productId + '" data-modification-type="' + modificationTypeId + '" data-modification-id="' + modificationId + '" for="' + modificationId + '">' + modification.value + '</label>' +
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
                                containerAllModifications += '<div class="pl-5 flex-center ingredient"><input checked class="hide" type="checkbox" id="' + ingredientId + '"><label for="' + ingredientId + '">~ ' + ingredient.title + '</label></div>';
                            }
                        });
                        i++;
                    });
                });
            } else {
                let ingredients = modification.ingredients;
                Object.keys(ingredients).forEach(function (ingredientId) {
                    let ingredient = ingredients[ingredientId];
                    containerAllModifications += '<div class="pl-5 flex-center ingredient"><input checked class="hide" type="checkbox" id="' + ingredientId + '"><label for="' + ingredientId + '">~ ' + ingredient.title + '</label></div>';
                });
            }
            containerAllModifications += '</div>';
            return containerAllModifications;
        }

        let allProducts = {!! json_encode($allProducts, JSON_UNESCAPED_UNICODE) !!};

    </script>

@stop
