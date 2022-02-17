@extends('app')

@section('content')

    <div class="mb-10">
        <a class="orange-button" href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div class="flex-wrap mb-10">
        <div class="mr-10 flex-center">
            <label>
                <span>Период</span>
                <input class="start-date" type="date" value="{{$startDate}}">
                <input class="end-date" type="date" value="{{$endDate}}">
            </label>
        </div>
    </div>

    <div>
        <div>
            <table class="w-100 border table-sort" id="products-modifications-table">
                <thead>
                <tr>
                    <th class="w-0">ID</th>
                    <th class="w-0">ID продукта</th>
                    <th class="w-0">Категория</th>
                    <th>Наименование</th>
                    <th class="w-0">Кол-во</th>
                    <th class="w-0">Стоимость одной</th>
                    <th class="w-0">Себестоимость одной</th>
                    <th class="w-0">Наценка</th>
                    <th class="w-0">Наценка %</th>
                    <th class="w-0">Ингредиенты</th>
                </tr>
                </thead>
                <tbody>
                @foreach($productsModifications as $id => $productModification)
                    <tr class="hover-color">
                        <td class="text-center">#{{$productModification->product_modifications_id}}</td>
                        <td class="text-center">#{{$productModification->product_id}}</td>
                        <td class="text-center">{{$productModification->category_title}}</td>
                        <td>{{$productModification->product_title . ' ' . $productModification->modification_title . ' ' . $productModification->modification_value}}</td>
                        <td class="text-center">{{$productModification->soldAmount}}</td>
                        <td class="text-center">{{$productModification->selling_price}}</td>
                        <td class="text-center">{{$productModification->costPrice}}</td>
                        <td class="text-center">{{$productModification->selling_price - $productModification->costPrice}}</td>
                        <td class="text-center">{{$productModification->margin}}&nbsp;%</td>
                        <td class="text-center">
                            <div class="product-modification-ingredients cp">Ингредиенты</div>
                            <div class="product-modification-ingredients-content hide">
                                <table class="white-border">
                                    <thead>
                                        <tr>
                                            <th>Наименование</th>
                                            <th>Стоимость ед.</th>
                                            <th>Кол-во</th>
                                            <th>Стоимость</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($productsModificationsIngredients[$productModification->product_modifications_id] as $productModificationIngredients)
                                        <tr>
                                            <td class="text-center">{{$productModificationIngredients->title}}</td>
                                            <td class="text-center">{{$productModificationIngredients->currentPrice}}</td>
                                            <td class="text-center">{{$productModificationIngredients->amount}}</td>
                                            <td class="text-center">{{$productModificationIngredients->currentPrice * $productModificationIngredients->amount}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="cp" onclick="fnExcelReport('products-modifications-table')" >Скачать</div>

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

        let changeDateFields = document.body.querySelectorAll('.start-date, .end-date');
        changeDateFields.forEach((changeDateField) => {
            changeDateField.addEventListener('change', () => {
                LoaderShow();
                let startDate = document.body.querySelector('.start-date').value;
                let endDate = document.body.querySelector('.end-date').value;
                if (startDate && endDate) {
                    location.href = "{{route('administrator-arm-products-modifications-page')}}?start-date=" + startDate + "&end-date=" + endDate;
                }
            });
        });
    </script>

@stop
