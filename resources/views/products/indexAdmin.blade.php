@extends('app')

@section('content')

    <div>

        @foreach($allProducts as $product)

            <div class="border m-10 p-10">

                <div class="product-title cp">{{'#'.$product->id . ' - ' . $product->title}}</div>

                <div class="container-product-modifications hide border m-10 p-10">

                    @foreach($product->Modifications as $modification)

                        <div class="border m-10 p-10">

                            <div class="product-modification cp">{{'#'.$modification->id . ' - ' . $modification->Modification->title . ' - ' . $modification->Modification->value . ' - ' . $modification->Modification->Type->value_unit}}</div>

                            <div class="container-product-modification hide border p-10 m-10">

                                <div class="border m-10 p-10">

                                    <div class="product-modification-ingredient cp">Ингредиенты</div>

                                    <div class="container-product-modification-ingredient hide border m-10 p-10">

                                        <div>

                                            @php
                                            $costPrice = 0;
                                            @endphp

                                            @foreach($modification->Ingredients as $ingredient)

                                                @php

                                                    $sumIngredient = $ingredient->ingredient_amount * $ingredient->Ingredient->CurrentPrice();
                                                    $costPrice += $sumIngredient;

                                                @endphp

                                                <div>{{'#'.$ingredient->Ingredient->id . ' - ' . $ingredient->Ingredient->title . ' - Кол-во: ' . $ingredient->ingredient_amount . ' - Цена: ' . $ingredient->Ingredient->CurrentPrice() . ' - Сумма: ' . $sumIngredient}}</div>

                                            @endforeach



                                        </div>
                                    </div>

                                </div>

                                <div>Себестоимость: {{$costPrice}}</div>

                                <div>Наценка: {{number_format(((($modification->selling_price - $costPrice ) / $costPrice) * 100), 2)}} %</div>

                                <div>Цена: {{$modification->selling_price}}</div>

                            </div>


                        </div>

                    @endforeach

                </div>

            </div>

        @endforeach

    </div>

@stop

@section('js')

    <script>

        document.body.querySelectorAll('.product-title').forEach((el) => {
            el.addEventListener('click', () => {
                let parentNode = el.parentNode;
                parentNode.querySelector('.container-product-modifications').showToggle();
            });
        });

        document.body.querySelectorAll('.product-modification').forEach((el) => {
            el.addEventListener('click', () => {
                let parentNode = el.parentNode;
                parentNode.querySelector('.container-product-modification').showToggle();
            });
        });

        document.body.querySelectorAll('.product-modification-ingredient').forEach((el) => {
            el.addEventListener('click', () => {
                let parentNode = el.parentNode;
                parentNode.querySelector('.container-product-modification-ingredient').showToggle();
            });
        });

    </script>

@stop
