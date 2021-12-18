@extends('app')

@section('content')

    <style>
        body {
            background-color: whitesmoke!important;
            color: black!important;
        }
        table, th, td {
            border: 1px solid black;
        }
        .modal-window-component-container .modal-window-component .modal-window-content-container {
            background-color: white;
        }
        .modal-window-component-container .modal-window-component .modal-window-content-container .modal-window-close-button path {
            fill: black;
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
                    <th>Категория</th>
                    <th>Наименование</th>
                    <th>Кол-во</th>
                    <th>Стоимость</th>
                    <th>Себестоимость</th>
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
