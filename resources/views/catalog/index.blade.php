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

    <div class="promo-container flex pb-20 m-10">
        <img class="border-radius-10" src="{{asset('promo/promo-1.jpg')}}" width="100%" alt="promo">
    </div>

    <div class="fast-menu pos-fix w-100 py-15 flex scroll-x-auto left-0 bg-black-custom" style="top: 50px; box-shadow: 0 0 10px white; transform: translate3d(0, 0, 0);">
        @foreach($allCategory as $category)
            <div class="clear-a color-orange px-15 navigation" data-anchor-id="{{$category->id}}">{{$category->title}}</div>
        @endforeach
    </div>

    <div class="flex-wrap catalog">

        @foreach($allProducts as $product)

            @if(!isset($category) || $product->categoryId !== $category)
                @php($category = $product->categoryId)
                <h2 class="w-100 ml-10 mb-10" id="{{$product->categoryId}}">{{$product->categoryTitle}}</h2>
            @endif

                @php($webpFile = (file_exists(public_path() . '/img/' . $product->id . '.webp') ? 'img/' . $product->id . '.webp' : 'img-pizza.png') . '?1')
                @php($imgFile = (file_exists(public_path() . '/img/jpg500/' . $product->id . '.jpg') ? 'img/jpg500/' . $product->id . '.jpg' : 'img-pizza.png') . '?1')

                <div class="button-open-product w-100 flex-column cp" data-product-id="{{$product->id}}" data-product-img-webp="{{url($webpFile)}}" data-product-img="{{url($imgFile)}}">

                    <div class="product-container flex-column border-orange scroll-off">

                        <div class="container-product-img-and-description">
                            <div class="container-product-img">
                                <picture>
                                    <source class="w-100" srcset="{{url($webpFile)}}" type="image/webp">
                                    <source class="w-100" srcset="{{url($imgFile)}}" type="image/jpeg">
                                    <img class="w-100" src="{{url($imgFile)}}" alt="{{$product->title}}">
                                </picture>
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

@stop

@section('js')

    <script>

        document.body.querySelectorAll('.button-open-product').forEach((el) => {
            el.addEventListener('click', () => {
                let productId = el.dataset.productId;
                let productImg = el.dataset.productImg;
                let productImgWebP = el.dataset.productImgWebp;
                ProductWindowGenerator(productId, productImg, productImgWebP);
            });
        });

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

@stop
