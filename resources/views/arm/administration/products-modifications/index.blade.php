@extends('app')

@section('content')

    <div class="mb-10">
        <a class="orange-button" href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div>
        <div>
            <div>Стоимость заказов : {{$sumOrders}}</div>
            <div>Себестоимость заказов : {{$costOrders}}</div>
        </div>
        <div>
            <table class="w-100 border table-sort">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Категория</th>
                    <th>Наименование</th>
                    <th>Кол-во</th>
                    <th>Стоимость одной</th>
                    <th>Себестоимость одной</th>
                    <th>Наценка</th>
                </tr>
                </thead>
                <tbody>
                @foreach($productsModifications as $id => $productModification)
                <tr>
                    <td>#{{$id}}</td>
                    <td>{{$productModification->categoryTitle}}</td>
                    <td>{{$productModification->title}}</td>
                    <td>{{$productModification->amount}}</td>
                    <td>{{$productModification->price}}</td>
                    <td>{{$productModification->costPrice}}</td>
                    <td>{{number_format(((($productModification->price - $productModification->costPrice ) / $productModification->costPrice) * 100), 2)}} %</td>
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
