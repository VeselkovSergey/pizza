@extends('app')

@section('content')

    <div>
        <a class="orange-button" href="{{route('chef-arm-orders-page')}}">Назад к заказам</a>

        <div class="mb-10">
            <button class="status-is-cooked order-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp order-status-5" data-url="{{route('chef-arm-change-status-order-to-cooked')}}">Приготовлен</button>
        </div>

        <div class="mb-10 p-5 order-status-{{$order->status_id}}">Заказ на кухне с {{$order->updated_at}} {{\App\Models\Orders::STATUS[$order->status_id]}}</div>

        <div>
            @foreach($productsModificationsInOrder as $productModificationInOrder)
                <?php /** @var \App\Models\ProductsModificationsInOrders $productModificationInOrder */ ?>
                <div class="product-container p-5 mb-10 product-in-order-status-{{$productModificationInOrder->status_id}}" data-order-product-status="{{$productModificationInOrder->status_id}}" data-product-id-in-order="{{$productModificationInOrder->id}}">
                    <div>{{\App\Models\ProductsModificationsInOrders::STATUS[$productModificationInOrder->status_id]}}</div>
                    <div>{{$productModificationInOrder->ProductModifications->Product->title . ' ' . $productModificationInOrder->ProductModifications->Modification->title . ' ' . $productModificationInOrder->ProductModifications->Modification->value}}</div>
                    <div>Кол-во: {{$productModificationInOrder->product_modification_amount}}</div>

                    <div class="bg-white color-black mt-10 mb-5 p-10">
                        <div class="toggle-button cp" data-toogle="status-log-container">Изменения статуса:</div>
                        <div class="ml-10 status-log-container">
                            @foreach($productModificationInOrder->Statuses as $orderProductStatus)
                                <?php /** @var \App\Models\OrdersStatusLogs $orderProductStatus */ ?>
                                <div class="p-5 m-5 flex-center-vertical flex-wrap" style="border: 1px solid grey;">
                                    <div class="mr-5" style="min-width: 200px">{{$orderProductStatus->created_at}}</div>
                                    <div class="mr-5 p-5 product-in-order-status-{{$orderProductStatus->old_status_id}}">{{\App\Models\ProductsModificationsInOrders::STATUS[$orderProductStatus->old_status_id]}}</div>
                                    <div class="mr-5">></div>
                                    <div class="mr-5 p-5 product-in-order-status-{{$orderProductStatus->new_status_id}}">{{\App\Models\ProductsModificationsInOrders::STATUS[$orderProductStatus->new_status_id]}}</div>
                                    <div class="mr-5">({{$orderProductStatus->User->surname . ' ' . $orderProductStatus->User->name. ' ' . $orderProductStatus->User->patronymic}})</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

{{--                    <div class="bg-white p-10">--}}
{{--                        @switch($productModificationInOrder->status_id)--}}
{{--                            @case(\App\Models\ProductsModificationsInOrders::STATUS_TEXT['new'])--}}
{{--                                <button class="order-product-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp product-in-order-status-2" data-url="{{route('chef-arm-change-status-product-to-chef-processes')}}">Взять в работу</button>--}}
{{--                                @break--}}
{{--                            @case(\App\Models\ProductsModificationsInOrders::STATUS_TEXT['chefProcesses'])--}}
{{--                                <button class="order-product-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp product-in-order-status-1" data-url="{{route('chef-arm-change-status-product-to-new')}}">Вернуть в статус: Не начинали готовить</button>--}}
{{--                                <button class="order-product-change-status clear-button py-5 px-25 mr-10 border-radius-5 cp product-in-order-status-3" data-url="{{route('chef-arm-change-status-product-to-cooked')}}">Приготовлен</button>--}}
{{--                            @break--}}
{{--                        @endswitch--}}
{{--                    </div>--}}
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
                    location.href = "{{route('chef-arm-orders-page')}}";
                });
            });
        });

        // let buttonsOrderProductChangeStatus = document.body.querySelectorAll('.order-product-change-status');
        // buttonsOrderProductChangeStatus.forEach((button) => {
        //     button.addEventListener('click', () => {
        //         let url = button.dataset.url;
        //         let productIdInOrder = button.closest('.product-container').dataset.productIdInOrder;
        //         Ajax(url, 'post', {productIdInOrder: productIdInOrder}).then(() => {
        //             // location.reload();
        //         });
        //     });
        // });

        {{--OrderProductsStatusCheck();--}}
        {{--function OrderProductsStatusCheck() {--}}
        {{--    let amountProductsWithStatusIsCooked = 0;--}}
        {{--    document.body.querySelectorAll('.product-container').forEach((product) => {--}}
        {{--        let productStatus = product.dataset.orderProductStatus;--}}
        {{--        if (parseInt(productStatus) === 3) {--}}
        {{--            amountProductsWithStatusIsCooked++;--}}
        {{--        }--}}
        {{--    });--}}
        {{--    if (amountProductsWithStatusIsCooked === {{sizeof($productsModificationsInOrder)}}) {--}}
        {{--        document.body.querySelector('button.status-is-cooked').show()--}}
        {{--    }--}}
        {{--}--}}

        ToggleShow();

    </script>

@stop
