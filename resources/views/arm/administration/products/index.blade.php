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
    </style>

    <div class="mb-10">
        <a href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div>
        <div>
            <table class="w-100 border table-sort">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Наименование</th>
                    <th>Категория</th>
                    <th>Описание</th>
                </tr>
                </thead>
                <tbody>
                @foreach($products as $product)
                    <?php /** @var \App\Models\Products $product */ ?>
                    <tr class="product-container" data-product-id="{{$product->id}}">
                        <td>#{{$product->id}}</td>
                        <td><input name="title" class="edit-field" readonly type="text" value="{{$product->title}}"></td>
                        <td>{{$product->Category->title}}</td>
                        <td><input name="description" class="edit-field w-100" readonly type="text" value="{{$product->description}}"></td>
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
                event.target.setAttribute('readonly', 'readonly');
                let productContainer = event.target.closest('.product-container');
                let productId = productContainer.dataset.productId;
                let value = {};
                value[event.target.name] = event.target.value;
                SaveChanges (productId, value);
            });
        });

        function SaveChanges (productId, data) {
            Ajax("{{route('administrator-arm-product-save-changes')}}", "POST", {productId: productId, data: JSON.stringify(data)}).then((response) => {
                FlashMessage(response.message);
            });
        }

    </script>

@stop
