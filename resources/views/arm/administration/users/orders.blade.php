@extends('app')

@section('content')

    <div class="mb-10">
        <a href="{{route('administrator-arm-users-page')}}">назад к пользователям</a>
    </div>

    <div>
        <div>Пользователь:</div>
        <div>
            <div># {{$user->id}}</div>
            <div>Имя: {{$user->name}}</div>
            <div>Телефон: {{$user->phone}}</div>
            <div>Кол-во заказов: {{$user->Orders->count()}}</div>
        </div>
        <div>
            <table class="w-100 border table-sort">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Статус</th>
                    <th>Дата создания</th>
                    <th>Дата изменения последнего статуса</th>
                    <th>Потрачено времени</th>
                    <th>Кол-во позиций</th>
                    <th>Курьер</th>
                    <th>Сумма</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    <?php /** @var \App\Models\Orders $order */?>
                    @php($productsModificationsInOrder = \App\Http\Controllers\Orders\OrdersController::OrderProductsModifications($order))
                    <tr>
                        <td>{{$order->id}}</td>
                        <td>{{\App\Models\Orders::STATUS[$order->status_id]}}</td>
                        <td>{{$order->created_at}}</td>
                        <td>{{$order->updated_at}}</td>
                        <td>{{date_diff($order->created_at, $order->updated_at)->format('%h:%i:%s')}}</td>
                        <td>{{$productsModificationsInOrder->count()}}</td>
                        <td>{{$order->courier_id}}</td>
                        <td>{{json_decode($order->all_information_raw_data)->orderSum}}</td>
                        <td class="text-center">
                            <div class="order-detail-info">Подробно</div>
                            <div class="order-detail-info-content hide">
                                @foreach($productsModificationsInOrder as $productModificationInOrder)
                                    <?php /** @var \App\Models\ProductsModificationsInOrders $productModificationInOrder */?>
                                    @php($productsAndModificationsInOrderForOrderEdit[] = (object)['productId' => $productModificationInOrder->ProductModifications->Product->id, 'modificationId' => $productModificationInOrder->product_modification_id, 'amount' => $productModificationInOrder->product_modification_amount, 'modificationTypeId' => $productModificationInOrder->ProductModifications->Modification->type_id])
                                    <div class="p-5 mb-10 product-in-order-status-{{$productModificationInOrder->status_id}}">
                                        <div>{{\App\Models\ProductsModificationsInOrders::STATUS[$productModificationInOrder->status_id]}}</div>
                                        <div>{{$productModificationInOrder->ProductModifications->Product->title . ' ' . $productModificationInOrder->ProductModifications->Modification->title . ' ' . $productModificationInOrder->ProductModifications->Modification->value}}</div>
                                        <div>{{'Цена: ' . $productModificationInOrder->ProductModifications->selling_price . ' ₽'}}</div>
                                        <div>Кол-во: {{$productModificationInOrder->product_modification_amount}}</div>
                                    </div>
                                @endforeach
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
