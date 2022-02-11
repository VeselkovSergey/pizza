<?php

$combos1 = [
    (object)[
        'id' => 1,
        'title' => 'Комбо - 25',
        'price' => '535',
        'sections' => [
            [
                (object)[
                    'productId' => 24,
                    'modificationId' => 7,
                ],
                (object)[
                    'productId' => 27,
                    'modificationId' => 16,
                ],
                (object)[
                    'productId' => 24,
                    'modificationId' => 7,
                ],
                (object)[
                    'productId' => 25,
                    'modificationId' => 10,
                ],
                (object)[
                    'productId' => 26,
                    'modificationId' => 13,
                ],
                (object)[
                    'productId' => 24,
                    'modificationId' => 7,
                ],
                (object)[
                    'productId' => 25,
                    'modificationId' => 10,
                ],
                (object)[
                    'productId' => 26,
                    'modificationId' => 13,
                ],
                (object)[
                    'productId' => 27,
                    'modificationId' => 16,
                ],
            ],
            [
                (object)[
                    'productId' => 24,
                    'modificationId' => 7,
                ],
                (object)[
                    'productId' => 25,
                    'modificationId' => 10,
                ],
                (object)[
                    'productId' => 27,
                    'modificationId' => 16,
                ],
                (object)[
                    'productId' => 24,
                    'modificationId' => 7,
                ],
                (object)[
                    'productId' => 25,
                    'modificationId' => 10,
                ],
                (object)[
                    'productId' => 26,
                    'modificationId' => 13,
                ],
                (object)[
                    'productId' => 24,
                    'modificationId' => 7,
                ],
                (object)[
                    'productId' => 25,
                    'modificationId' => 10,
                ],
                (object)[
                    'productId' => 26,
                    'modificationId' => 13,
                ],
                (object)[
                    'productId' => 24,
                    'modificationId' => 7,
                ],
                (object)[
                    'productId' => 25,
                    'modificationId' => 10,
                ],
                (object)[
                    'productId' => 26,
                    'modificationId' => 13,
                ],
                (object)[
                    'productId' => 27,
                    'modificationId' => 16,
                ],
                (object)[
                    'productId' => 24,
                    'modificationId' => 7,
                ],
                (object)[
                    'productId' => 25,
                    'modificationId' => 10,
                ],
                (object)[
                    'productId' => 26,
                    'modificationId' => 13,
                ],
                (object)[
                    'productId' => 27,
                    'modificationId' => 16,
                ],
                (object)[
                    'productId' => 24,
                    'modificationId' => 7,
                ],
                (object)[
                    'productId' => 25,
                    'modificationId' => 10,
                ],
                (object)[
                    'productId' => 26,
                    'modificationId' => 13,
                ],
                (object)[
                    'productId' => 27,
                    'modificationId' => 16,
                ],
            ],
            [
                (object)[
                    'productId' => 24,
                    'modificationId' => 7,
                ],
            ],
        ]
    ],
];

?>

@extends('app')

@section('content')

    <style>

        input[type="radio"]:checked.modification-input + label {
            /*background-color: #FFFFFF;*/
            background-color: #010101;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
        }

        .modifications-container:not(.hide) {
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

        .popular-and-new-position-container {
            top: 20px;
            right: 20px;
        }

        .spicy-container {
            top: 15px;
            left: 10px;
        }

        @media screen and (max-width: 540px) {
            .popular-and-new-position-container {
                top: 10px;
                left: 10px;
                right: unset;
            }

            .spicy-container {
                top: 5px;
                left: unset;
                right: 5px;
            }

            .spicy-container > img {
                width: 15px;
            }

            .slots-container {
                display: none;
            }
        }

        .popular-and-new-position {
            padding: 2px 4px;
            border-radius: 5px;
            text-align: center;
        }

        .popular-position-bg-color {
            background-color: red;
        }

        .new-position-bg-color {
            background-color: green;
        }

    </style>

    <style>
        .section-container::-webkit-scrollbar-thumb {
            background-color: #ff7300;
            border-radius: 2px;
            border: 1px solid hsla(0,0%,50.2%,.1);
        }
        .section-container::-webkit-scrollbar-track {
            background: hsla(0,0%,50.2%,.1);
        }
        .section-container::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .sections-container::-webkit-scrollbar-thumb {
            background-color: #ff7300;
            border-radius: 2px;
            border: 1px solid hsla(0,0%,50.2%,.1);
        }
        .sections-container::-webkit-scrollbar-track {
            background: hsla(0,0%,50.2%,.1);
        }
        .sections-container::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .section-container:not(.hide) {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .slot:hover {
            /*animation: long-hover-bg-color 500ms forwards;*/
            /*border-width: 5px;*/
            transform: scale(0.95);
        }

        .fake-hide {
            display: none;
        }

        @keyframes long-hover-bg-color {
            0% {
                border-width: 1px;
            }
            100% {
                border-width: 3px;
            }
         }

        .combo-content-main {
            max-height: 70vh;
        }

        @media screen and (max-width: 960px) {
            .slots-container {
                display: none;
            }
            .sections-container {
                width: 100%;
                flex-direction: column;
                overflow-y: hidden;
            }
            .section-container {
                display: flex;
                overflow-x: scroll;
                justify-content: unset;
            }

            .combo-content-main {
                max-height: unset;
            }

            .fake-hide {
                display: flex;
            }
        }
        @media screen and (max-width: 540px) {
            .combo-content-main {
                max-height: unset;
            }
        }

        input[type="radio"]:checked.for-combo + span {
            border-radius: 30px;
            background-color: unset;
            border: 1px solid green;
            display: flex;
            align-items: center;
            justify-content: center;
            right: 0;
            top: 0;
            padding: 3px;
            color: green;
        }
        input[type="radio"]:checked.for-combo + span > svg {
            width: 30px;
            height: 30px;
        }
    </style>

    <div class="promo-container flex pb-20 m-10">
        <img class="border-radius-10" src="{{asset('promo/promo-2.png')}}" width="100%" alt="promo">
    </div>

    <div class="flex-wrap catalog">

        <h2 class="w-100 ml-10 mb-10" id="0">Комбо</h2>

        @foreach($combos as $combo)

            <div class="combo-container w-100 flex-column cp" data-combo-id="{{$combo->id}}">

                <div class="product-container flex-column border-orange scroll-off" style="background-color: #00000090">

                    <div class="container-product-img-and-description">
                        <div class="container-product-img">
                            <picture>
                                <source class="w-100" srcset="{{asset($combo->id.'-combo.png')}}" type="image/webp">
                                <source class="w-100" srcset="{{asset($combo->id.'-combo.png')}}" type="image/jpeg">
                                <img class="w-100" src="{{asset($combo->id.'-combo.png')}}" alt="{{$combo->title}}">
                            </picture>
                        </div>

                        <div class="container-product-description flex-column-center p-10">
                            <div>
                                <div class="text-center mb-10">{{$combo->title}}</div>
                            </div>
                            <button class="w-100 bg-orange color-white border-radius-5 clear-button p-5 mt-a cp">{{$combo->price}} ₽</button>
                        </div>
                    </div>

                </div>

            </div>

        @endforeach

        @foreach($allProducts as $product)

            @if(!isset($category) || $product->categoryId !== $category)
                @php($category = $product->categoryId)
                <h2 class="w-100 ml-10 mb-10" id="{{$product->categoryId}}">{{$product->categoryTitle}}</h2>
            @endif

            <div class="button-open-product w-100 flex-column cp" data-product-id="{{$product->id}}">

                @if($product->isPopular || $product->isNew || $product->isSpicy)
                    <div class="pos-rel">
                        <div class="pos-abs popular-and-new-position-container">
                            @if($product->isPopular)
                                <div class="mb-5 popular-and-new-position popular-position-bg-color">HIT</div>
                            @endif
                            @if($product->isNew)
                                <div class="popular-and-new-position new-position-bg-color">NEW</div>
                            @endif
                        </div>
                        @if($product->isSpicy)
                            <div class="pos-abs spicy-container">
                                @for($i = 0; $i < $product->isSpicy; $i++)
                                    <img width="25" src="{{asset('spicy.png')}}" alt="">
                                @endfor
                            </div>
                        @endif
                    </div>
                @endif

                <div class="product-container flex-column border-orange scroll-off" style="background-color: #00000090">

                    <div class="container-product-img-and-description">
                        <div class="container-product-img">
                            <picture>
                                <source class="w-100" srcset="{{$product->imgUrl}}" type="image/webp">
                                <source class="w-100" srcset="{{$product->imgUrl}}" type="image/jpeg">
                                <img class="w-100" src="{{$product->imgUrl}}" alt="{{$product->title}}">
                            </picture>
                        </div>

                        <div class="container-product-description flex-column-center p-10">
                            <div>
                                <div class="text-center mb-10">{{$product->title}}</div>
                                <div class="text-center" style="font-weight: 400; font-size: 12px;">{{$product->description}}</div>
                            </div>
                            <button class="w-100 bg-orange color-white border-radius-5 clear-button p-5 mt-a cp">{{$product->modificationCount > 1 ? 'от' : ''}} {{$product->minimumPrice}}
                                ₽
                            </button>
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
                let productId = parseInt(el.dataset.productId);
                ProductWindowGenerator(productId);
            });
        });

        document.body.querySelectorAll('.combo-container').forEach((el) => {
            el.addEventListener('click', () => {
                let comboId = parseInt(el.dataset.comboId);
                ComboWindowGenerator(comboId);
            });
        });

        function ComboWindowGenerator(comboId) {
            const combo = allCombos.find(combo => combo.id === comboId);
            const comboTitle = combo.title;
            const comboPrice = combo.price;
            const comboSections = combo.sections;

            const comboContainer = CreateElement('div', {class: 'combo-content'});
            CreateElement('div', {content: comboTitle}, comboContainer);
            const sectionMainContainer = CreateElement('div', {class: 'flex combo-content-main'}, comboContainer);
            const sectionMain2Container = CreateElement('div', {class: 'w-75 sections-container scroll-y-auto'}, sectionMainContainer);
            const sectionMain3Container = CreateElement('div', {class: 'w-25 slots-container ml-10 scroll-y-auto', attr: {style: 'min-width: 250px;'}}, sectionMainContainer);

            Object.keys(comboSections).forEach((i) => {
                const section = comboSections[i];

                const sectionButton = CreateElement('div', {content: 'Изменить слот', class: 'slot cp flex-center border-orange border-radius-10 mb-10', attr: {style: 'height: 200px;'}}, sectionMain3Container);
                sectionButton.dataset.sectionId = i;
                sectionButton.addEventListener('click', () => {
                    sectionMain2Container.querySelectorAll('.section-container').forEach((el) => {
                        el.hide();
                    });
                    sectionMain2Container.querySelector('.section-container[data-section-id="'+sectionButton.dataset.sectionId+'"]').show();
                    buttonContainer.classList.remove('fake-hide');
                });

                const sectionContainer = CreateElement('div', {class: 'section-container hide mb-10 pb-5'}, sectionMain2Container);
                sectionContainer.dataset.sectionId = i;

                let checked = true;

                Object.keys(section).forEach((j) => {

                    const data = section[j];
                    const productId = data.productId;
                    const modificationId = data.modificationId;

                    const product = allProducts[productId];
                    const productImg = product.imgUrl;
                    const modifications = product.modifications;
                    const modification = modifications.find(modification => modification.id === modificationId);
                    const modificationTitle = modification.title;

                    const productContainer = CreateElement('label', {class: 'cp pos-rel w-25', attr: {for: 'section-' + i + '-product-' + j, style: 'min-width: 200px; min-height: 265px;'}}, sectionContainer);
                    productContainer.dataset.sectionId = i;
                    productContainer.addEventListener('click', () => {
                        sectionMain3Container.querySelector('.slot[data-section-id="'+productContainer.dataset.sectionId+'"]').innerHTML = '<img width="200" height="200" src="'+productImg+'">';
                    });

                    const productInput = CreateElement('input', {class: 'for-combo hide', attr: {type: 'radio', name: 'section-' + i, id: 'section-' + i + '-product-' + j}}, productContainer);

                    productInput.dataset.productId = productId;
                    productInput.dataset.modificationId = modificationId;

                    if (checked) {
                        productInput.setAttribute('checked', 'true');
                        checked = false;
                    }

                    const productSpan = CreateElement('span', {class: 'pos-abs hide', content: SvgCheckedButton}, productContainer);
                    const productImgElement = CreateElement('img', {attr: {src: productImg, style: 'width: 200px;'}}, productContainer);
                    const productTitleElement = CreateElement('div', {content: modificationTitle, class: 'text-center'}, productContainer);

                });
            });

            const buttonContainer = CreateElement('div', {class: 'flex-center fake-hide container-button-put-in-basket mt-25'}, comboContainer);
            const putInBasketButton = CreateElement('button', {class: 'orange-button', content: 'Добавить в корзину за ' + comboPrice + ' ₽'}, buttonContainer);
            putInBasketButton.addEventListener('click', () => {
                let products = [];
                comboContainer.querySelectorAll('input[type="radio"]:checked').forEach((input) => {
                    const productId = parseInt(input.dataset.productId);
                    const modificationId = parseInt(input.dataset.modificationId);
                    const product = allProducts[productId];
                    const modifications = product.modifications;
                    const modification = modifications.find(modification => modification.id === modificationId);
                    const modificationTitle = modification.title;

                    products.push({
                        productId: productId,
                        modificationId: modificationId,
                        modificationTitle: modificationTitle,
                    })
                });
                products = products.sort((prev, next) => prev.modificationId - next.modificationId);
                let key = 'combo-' + comboId;
                let title = '<div>'+comboTitle+'</div>' + '<div class="product-in-combo">(';
                products.forEach((data) => {
                    key += '-' + data.modificationId;
                    title += ' / ' + data.modificationTitle
                });
                title += ' )</div>';

                AddItemInBasket(key, {
                    title: title,
                    price: comboPrice,
                    combo: products
                });
                FlashMessage(comboTitle);
                CloseModal(modal);
            });

            const modal = ModalWindow(comboContainer);
        }

        const allCombos = {!! json_encode($combos, JSON_UNESCAPED_UNICODE) !!};
        allProducts = {!! json_encode($allProducts, JSON_UNESCAPED_UNICODE) !!};

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

@stop
