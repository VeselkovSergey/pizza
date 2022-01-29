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

        .popular-and-new-position-container {
             top: 20px;
             right: 20px;
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

    <div class="promo-container flex pb-20 m-10">
        <img class="border-radius-10" src="{{asset('promo/promo-1.jpg')}}" width="100%" alt="promo">
    </div>

    <div class="flex-wrap catalog">

        @foreach($allProducts as $product)

            @if(!isset($category) || $product->categoryId !== $category)
                @php($category = $product->categoryId)
                <h2 class="w-100 ml-10 mb-10" id="{{$product->categoryId}}">{{$product->categoryTitle}}</h2>
            @endif

            @php($webpFile = (file_exists(public_path() . '/img/png/' . $product->id . '.png') ? 'img/png/' . $product->id . '.png' : 'img-pizza.png') . '?1')
            @php($imgFile = (file_exists(public_path() . '/img/jpg500/' . $product->id . '.jpg') ? 'img/jpg500/' . $product->id . '.jpg' : 'img-pizza.png') . '?1')

            <div class="button-open-product w-100 flex-column cp" data-product-id="{{$product->id}}"
                 data-product-img-webp="{{url($webpFile)}}" data-product-img="{{url($imgFile)}}">

                @if($product->is_popular || $product->is_new)
                    <div class="pos-rel">
                        <div class="pos-abs popular-and-new-position-container">
                            @if($product->is_popular)
                                <div class="mb-5 popular-and-new-position popular-position-bg-color">HIT</div>
                            @endif
                            @if($product->is_new)
                                <div class="popular-and-new-position new-position-bg-color">NEW</div>
                            @endif
                        </div>
                    </div>
                @endif

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
