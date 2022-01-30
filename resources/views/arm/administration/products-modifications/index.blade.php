@extends('app')

@section('content')

    <style>
        table.white-border {
            border: 1px solid #ffffff;
        }
        table.white-border th {
            border: 1px solid #ffffff;
        }
        table.white-border td {
            border: 1px solid #ffffff;
        }
    </style>

    <div class="mb-10">
        <a class="orange-button" href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div>
        <div>
            <table class="w-100 border table-sort">
                <thead>
                <tr>
                    <th class="w-0">ID</th>
                    <th class="w-0">Категория</th>
                    <th>Наименование</th>
                    <th class="w-0">Кол-во</th>
                    <th class="w-0">Стоимость одной</th>
                    <th class="w-0">Себестоимость одной</th>
                    <th class="w-0">Наценка</th>
                    <th class="w-0">Ингредиенты</th>
                </tr>
                </thead>
                <tbody>
                @foreach($productsModifications as $id => $productModification)
                <tr class="hover-color">
                    <td class="text-center">#{{$productModification->product_modifications_id}}</td>
                    <td class="text-center">{{$productModification->category_title}}</td>
                    <td>{{$productModification->product_title . ' ' . $productModification->modification_title . ' ' . $productModification->modification_value}}</td>
                    <td class="text-center">{{$productModification->soldAmount}}</td>
                    <td class="text-center">{{$productModification->selling_price}}</td>
                    <td class="text-center">{{$productModification->costPrice}}</td>
                    <td class="text-center">{{$productModification->margin}}&nbsp;%</td>
                    <td class="text-center">
                        <div class="product-modification-ingredients">Ингредиенты</div>
                        <div class="product-modification-ingredients-content hide">
                            <table class="white-border">
                                <tr>
                                    <th>Наименование</th>
                                    <th>Стоимость ед.</th>
                                    <th>Кол-во</th>
                                    <th>Стоимость</th>
                                </tr>
                                @foreach($productsModificationsIngredients[$productModification->product_modifications_id] as $productModificationIngredients)
                                <tr>
                                    <td class="text-center">{{$productModificationIngredients->title}}</td>
                                    <td class="text-center">{{$productModificationIngredients->currentPrice}}</td>
                                    <td class="text-center">{{$productModificationIngredients->amount}}</td>
                                    <td class="text-center">{{$productModificationIngredients->currentPrice * $productModificationIngredients->amount}}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@stop

@section('js')

    <script>
        let productModificationIngredientsButtons = document.body.querySelectorAll('.product-modification-ingredients');
        productModificationIngredientsButtons.forEach((productModificationIngredientsButton) => {
            productModificationIngredientsButton.addEventListener('click', (event) => {
                let productModificationIngredientsContent = event.target.nextElementSibling.innerHTML;
                let modal = ModalWindow(productModificationIngredientsContent);
                modal.querySelector('.product-modification-ingredients-content').show();
            });
        });
    </script>

@stop
