@extends('app')

@section('content')

    <style>
        .edit-field {
            cursor: pointer;
            width: -webkit-fill-available;
        }
        .edit-field:not(:read-only) {
            transform: scale(1.2);
        }
        .hover-color:hover {
             background-color: wheat;
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
                    <th>Наименование</th>
                    <th class="w-0">Категория</th>
                    <th>Описание</th>
                    <th class="w-0">Доп. покупки</th>
                    <th class="w-0">Новинка</th>
                    <th class="w-0">Порядок в доп покупках</th>
                </tr>
                </thead>
                <tbody>
                @foreach($products as $product)
                    <?php /** @var \App\Models\Products $product */ ?>
                    <tr class="product-container hover-color" data-product-id="{{$product->id}}">
                        <td>#{{$product->id}}</td>
                        <td><input name="title" class="edit-field" readonly type="text" value="{{$product->title}}"></td>
                        <td class="text-center">{{$product->Category->title}}</td>
                        <td><input name="description" class="edit-field w-100" readonly type="text" value="{{$product->description}}"></td>
                        <td>
                            <div class="flex-center">
                                <label class="custom-checkbox-label" for="is_additional_sales-{{$product->id}}">
                                    <input class="edit-field" type="checkbox" id="is_additional_sales-{{$product->id}}" name="is_additional_sales" @if($product->is_additional_sales) checked @endif/>
                                    <div class="custom-checkbox-slider round"></div>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="flex-center">
                                <label class="custom-checkbox-label" for="is_new-{{$product->id}}">
                                    <input class="edit-field" type="checkbox" id="is_new-{{$product->id}}" name="is_new" @if($product->is_new) checked @endif/>
                                    <div class="custom-checkbox-slider round"></div>
                                </label>
                            </div>
                        </td>
                        <td><input name="additional_sales_sort" class="edit-field w-100" readonly type="text" value="{{$product->additional_sales_sort}}"></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>


@stop

@section('js')

    <script>

        document.body.querySelectorAll('.edit-field').forEach((field) => {
            field.addEventListener('dblclick', (event) => {
                event.target.removeAttribute('readonly');
            });

            field.addEventListener('blur', (event) => {
                if (event.target.getAttribute('type') !== 'checkbox') {
                    event.target.setAttribute('readonly', 'readonly');
                    let productContainer = event.target.closest('.product-container');
                    let productId = productContainer.dataset.productId;
                    let value = {};
                    value[event.target.name] = event.target.value;
                    SaveChanges (productId, value);
                }
            });

            field.addEventListener('change', (event) => {
                if (event.target.getAttribute('type') === 'checkbox') {
                    let productContainer = event.target.closest('.product-container');
                    let productId = productContainer.dataset.productId;
                    let value = {};
                    value[event.target.name] = event.target.checked;
                    SaveChanges (productId, value);
                }
            });
        });

        function SaveChanges (productId, data) {
            Ajax("{{route('administrator-arm-product-save-changes')}}", "POST", {productId: productId, data: JSON.stringify(data)}).then((response) => {
                FlashMessage(response.message);
            });
        }

    </script>

@stop
