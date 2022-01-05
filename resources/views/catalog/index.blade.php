@extends('app')

@section('content')

    <style>

        input[type="radio"]:checked.modification-input + label {
            /*background-color: #FFFFFF;*/
            background-color: #010101;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
        }
        .container-modification {
            /*background-color: rgb(243, 243, 247);*/
            background-color: #434343;
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

        .ingredient:not(:last-child):after {
            content: ', ';
        }

    </style>

    <div class="fast-menu pos-fix w-100 py-15 flex scroll-x-auto left-0 bg-black-custom" style="top: 50px; box-shadow: 0 0 10px white;">
        @foreach($allCategory as $category)
            <div class="clear-a color-orange px-15 navigation" data-anchor-id="{{$category->id}}">{{$category->title}}</div>
        @endforeach
    </div>

    <div class="flex-wrap catalog">

        @foreach($allProducts as $product)

            @if(!isset($category) || $product->categoryId !== $category)
                @php($category = $product->categoryId)
                <div class="w-100 ml-10 mb-10" id="{{$product->categoryId}}">{{$product->categoryTitle}}</div>
            @endif

                @php($imgFile = (file_exists(public_path() . '/img/' . $product->id . '.webp') ? 'img/' . $product->id . '.webp' : 'img-pizza.png'))

                <div class="button-open-product w-100 flex-column cp" data-product-id="{{$product->id}}" data-product-img="{{url($imgFile)}}">

                    <div class="product-container flex-column border-orange scroll-off">

                        <div class="container-product-img-and-description">
                            <div class="container-product-img">
                                <img src="{{url($imgFile)}}" class="w-100" alt="{{$product->title}}">
                            </div>

                            <div class="container-product-description flex-column-center p-10">
                                <div>
                                    <div class="text-center mb-10">{{$product->title}}</div>
                                    <div style="font-weight: 400; font-size: 12px;">{{$product->description}}</div>
                                </div>
                                <button class="w-100 bg-orange color-white border-radius-5 clear-button p-5 mt-a cp">{{$product->modificationCount > 1 ? 'от' : ''}} {{$product->minimumPrice}} ₽</button>
                            </div>
                        </div>


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
                let productImg = el.dataset.productImg;
                ProductWindowGenerator(productId, productImg);
            });
        });

        let modificationSelected = null;
        function ProductWindowGenerator(productId, productImg) {

            let productTitle = allProducts['product-'+productId].title;

            let imgUrl = productImg;

            let productContent = document.createElement('div');
            productContent.className = 'flex product-content h-100';
            productContent.innerHTML =
                                '<div class="container-img-and-about-product">' +
                                    '<div class="w-100">' +
                                        '<div>' +
                                            '<img src="' + imgUrl + '" class="w-100" alt="">' +
                                        '</div>' +
                                        // '<p>Традиционное итальянское блюдо в виде тонкой круглой лепёшки (пирога) из дрожжевого теста, выпекаемой с уложенной сверху начинкой из томатного соуса, кусочков сыра, мяса, овощей, грибов и других продуктов.</p>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="container-modification-product flex" style="flex: 1;">' +
                                    '<div class="w-100 flex-column h-100">' +
                                        '<div class="text-center text-up">'+productTitle+'</div>' +
                                        '<div class="container-ingredients text-down">' +
                                            IngredientsGenerator(productId) +
                                        '</div>'+
                                        ModificationsGenerate(productId) +
                                        '<div class="container-button-put-in-basket mt-a mx-a" style="padding-bottom: 50px;"><button class="button-put-in-basket btn first mt-25">В корзину</button></div>' +
                                    '</div>' +
                                '</div>';

            let buttonPutInBasket = productContent.querySelector('.button-put-in-basket');
            buttonPutInBasket.innerHTML = 'Добавить в корзину за ' + startSellingPriceModification + ' ₽';

            productContent.querySelectorAll('.modification-button').forEach((el) => {
                el.addEventListener('click', () => {
                    let productId = el.dataset.productId;
                    let modificationType = el.dataset.modificationType;
                    let modificationId = el.dataset.modificationId;
                    let stopList = el.dataset.stopList;

                    let modification = allProducts[productId]['modifications'][modificationType][modificationId];
                    let sellingPriceModification = modification.sellingPrice;
                    let ingredients = IngredientsGenerator(null, modification);
                    let containerIngredients = productContent.querySelector('.container-ingredients');
                    containerIngredients.innerHTML = ingredients;
                    buttonPutInBasket.innerHTML = 'Добавить в корзину за ' + sellingPriceModification + ' ₽';
                    modificationSelected = {
                        product: allProducts[productId],
                        modification: allProducts[productId]['modifications'][modificationType][modificationId],
                        stopList: stopList,
                    }
                });
            });

            buttonPutInBasket.addEventListener('click', () => {
                if (modificationSelected.stopList === 1) {
                    ModalWindow('Позиция находится в стоп листе. Приносим свои извинения.');
                    return;
                }
                FlashMessage('Добавлено: <br/>' + modificationSelected.product.title + (modificationSelected.modification.value !== 'Отсутствует' ? (', ' + modificationSelected.modification.title + ' ' + modificationSelected.modification.value) : ''));
                AddProductInBasket(modificationSelected);
                modalWindow.slowRemove();
                document.body.classList.remove('scroll-off');
            });

            let modalWindow = ModalWindow(productContent);
        }

        let startSellingPriceModification = 0;
        function ModificationsGenerate(productId) {
            let containerAllModificationsTemp = '';
            let disableModificationContainer = false;
            let stopList = false;
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
                            stopList: modification.stop_list,
                        }
                    }

                    if (modification.value === 'Отсутствует') {
                        disableModificationContainer = true;
                    }

                    if (modification.stop_list === 1) {
                        stopList = true;
                    }

                    let buttonWidth = 'width:' + (100 / modification.modificationTypeCount) + '%;';
                    let modificationIdHTML =
                        '<div class="text-center flex" style="' + buttonWidth + '">' +
                            '<input name="' + modificationTypeId + '" class="hide modification-input" id="' + modificationId + '" type="radio" ' + checkedInput + '/>' +
                            // '<label class="modification-button"data-product-id="product-' + productId + '" data-modification-type="' + modificationTypeId + '" data-modification-id="' + modificationId + '" for="' + modificationId + '">' + modification.title + ' - ' + modification.value + '</label>' +
                            '<label class="modification-button" data-stop-list="' + modification.stop_list + '" data-product-id="product-' + productId + '" data-modification-type="' + modificationTypeId + '" data-modification-id="' + modificationId + '" for="' + modificationId + '">' + modification.value + '</label>' +
                        '</div>';
                    modificationTypeHTML += modificationIdHTML;
                    i++;
                });
                modificationTypeHTML += '</div>';
                containerAllModificationsTemp += modificationTypeHTML;
            });
            let containerAllModifications;
            if (disableModificationContainer) {
                containerAllModifications = '<div class="hide">'+ containerAllModificationsTemp +'</div>';
            } else {
                containerAllModifications = '<div>'+ containerAllModificationsTemp +'</div>';
            }

            if (stopList) {
                setTimeout(() => {
                    ModalWindow('Позиция находится в стоп листе. Приносим свои извинения.');
                }, 200);
            }

            return containerAllModifications;
        }

        function IngredientsGenerator(productId, modification) {
            let containerAllModifications = '<div class="flex-wrap-center">';
            if (modification === undefined) {
                Object.keys(allProducts['product-'+productId]['modifications']).forEach(function (modificationTypeId) {
                    let modificationType = allProducts['product-'+productId]['modifications'][modificationTypeId];
                    let i = 0;
                    Object.keys(modificationType).forEach(function (modificationId) {
                        let modification = modificationType[modificationId];
                        let ingredients = modification.ingredients;
                        Object.keys(ingredients).forEach(function (ingredientId) {
                            let ingredient = ingredients[ingredientId];
                            if (ingredient.visible !== 0) {
                                if (i === 0) {
                                    containerAllModifications += '<div class="pl-5 flex-center ingredient"><input checked class="hide" type="checkbox" id="' + ingredientId + '"><label class="ingredient-title" for="' + ingredientId + '">' + ingredient.title + '</label></div>';
                                }
                            }
                        });
                        i++;
                    });
                });
            } else {
                let ingredients = modification.ingredients;
                Object.keys(ingredients).forEach(function (ingredientId) {
                    let ingredient = ingredients[ingredientId];
                    if (ingredient.visible !== 0) {
                        containerAllModifications += '<div class="pl-5 flex-center ingredient"><input checked class="hide" type="checkbox" id="' + ingredientId + '"><label class="ingredient-title" for="' + ingredientId + '">' + ingredient.title + '</label></div>';
                    }
                });
            }
            containerAllModifications += '</div>';
            return containerAllModifications;
        }

        let allProducts = {!! json_encode($allProducts, JSON_UNESCAPED_UNICODE) !!};

        document.body.querySelectorAll('.navigation').forEach((anchor) => {
            anchor.addEventListener('click', (event) => {
                let el = document.getElementById(event.target.dataset.anchorId);
                window.scroll({
                    top: el.offsetTop - 109,
                    behavior: 'smooth'
                });
            });
        });

    </script>

    @if(!(auth()->check() && auth()->user()->IsManager()))

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(86929115, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true
        });
    </script>
    <!-- /Yandex.Metrika counter -->

    @endif

@stop
