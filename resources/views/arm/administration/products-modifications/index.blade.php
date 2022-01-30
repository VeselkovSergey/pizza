@extends('app')

@section('content')

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
                </tr>
                </thead>
                <tbody>
                @foreach($productsModifications as $id => $productModification)
                <tr class="hover-color">
                    <td class="text-center">#{{$id}}</td>
                    <td class="text-center">{{$productModification->category_title}}</td>
                    <td>{{$productModification->product_title . ' ' . $productModification->modification_title . ' ' . $productModification->modification_value}}</td>
                    <td class="text-center">{{$productModification->soldAmount}}</td>
                    <td class="text-center">{{$productModification->selling_price}}</td>
                    <td class="text-center">{{$productModification->costPrice}}</td>
                    <td class="text-center">{{$productModification->margin}}&nbsp;%</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@stop

@section('js')

    <script>
        let orderDetailInfoButtons = document.body.querySelectorAll('.order-detail-info');
        orderDetailInfoButtons.forEach((orderDetailInfoButton) => {
            orderDetailInfoButton.addEventListener('click', (event) => {
                let orderDetailInfoContent = event.target.nextElementSibling.innerHTML;
                let modal = ModalWindow(orderDetailInfoContent);
                modal.querySelector('.order-detail-info-content').show();
            });
        });
    </script>

@stop
