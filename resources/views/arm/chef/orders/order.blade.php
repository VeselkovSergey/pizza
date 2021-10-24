@extends('app')

@section('content')

    <div>
        <a class="block mb-10" href="{{route('chef-arm-orders-page')}}">Назад к заказам</a>

        <div class="mb-10">
            <button class="order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-5" data-url="{{route('chef-arm-change-status-order-to-cooked')}}">Приготовлен</button>
        </div>

        <div class="mb-10 p-5 order-status-{{$order->status_id}}">Заказ на кухне с {{$order->updated_at}} {{\App\Models\Orders::STATUS[$order->status_id]}}</div>

        <div>
            @foreach($productsModificationsInOrder as $productModificationInOrder)
                <div class="product-container p-5 mb-10 product-in-order-status-{{$productModificationInOrder->status_id}}" data-product-id-in-order="{{$productModificationInOrder->id}}">
                    <div>{{\App\Models\ProductsModificationsInOrders::STATUS[$productModificationInOrder->status_id]}}</div>
                    <div>{{$productModificationInOrder->ProductModifications->Product->title . ' ' . $productModificationInOrder->ProductModifications->Modification->title . ' ' . $productModificationInOrder->ProductModifications->Modification->value}}</div>
                    <div>Кол-во: {{$productModificationInOrder->product_modification_amount}}</div>

                    <div class="bg-white color-black mt-10 mb-5 p-10">
                        <div>Изменения статуса:</div>
                        <div class="ml-10">
                            @foreach($productModificationInOrder->Statusses as $orderProductStatus)
                                <div class="p-5 m-5">{{$orderProductStatus->created_at}}
                                    <span class="p-5 product-in-order-status-{{$orderProductStatus->old_status_id}}">{{\App\Models\ProductsModificationsInOrders::STATUS[$orderProductStatus->old_status_id]}}</span>
                                    >
                                    <span class="p-5 product-in-order-status-{{$orderProductStatus->new_status_id}}">{{\App\Models\ProductsModificationsInOrders::STATUS[$orderProductStatus->new_status_id]}}</span>
                                    ({{$orderProductStatus->User->surname . ' ' . $orderProductStatus->User->name. ' ' . $orderProductStatus->User->patronymic}})
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white p-10">
                        <button class="order-product-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp product-in-order-status-1" data-url="{{route('chef-arm-change-status-product-to-new')}}">Вернуть в статус: Не начинали готовить</button>
                        <button class="order-product-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp product-in-order-status-2" data-url="{{route('chef-arm-change-status-product-to-chef-processes')}}">Взять в работу</button>
                        <button class="order-product-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp product-in-order-status-3" data-url="{{route('chef-arm-change-status-product-to-cooked')}}">Приготовлен</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@stop

@section('js')

    <script>
        let buttonsOrderChangeStatus = document.body.querySelectorAll('.order-change-status');
        buttonsOrderChangeStatus.forEach((button) => {
            button.addEventListener('click', () => {
                let url = button.dataset.url;
                Ajax(url, 'post', {orderId: {{$order->id}}}).then(() => {
                    location.reload();
                });
            });
        });

        let buttonsOrderProductChangeStatus = document.body.querySelectorAll('.order-product-change-status');
        buttonsOrderProductChangeStatus.forEach((button) => {
            button.addEventListener('click', () => {
                let url = button.dataset.url;
                let productIdInOrder = button.closest('.product-container').dataset.productIdInOrder;
                Ajax(url, 'post', {productIdInOrder: productIdInOrder}).then(() => {
                    location.reload();
                });
            });
        });
    </script>

@stop