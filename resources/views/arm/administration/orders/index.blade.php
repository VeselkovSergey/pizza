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

    @php($sum = 0)
    @php($sumCash = 0)
    @php($sumBank = 0)
    @php($sumCost = 0)
    @php($ordersCreatorWeb = 0)
    @php($ordersCreatorManager = 0)
    @php($amountOrdersInDays = [])
    @php($sumOrdersInDays = [])

    <div class="flex-column">
        <div style="order: 2;">
            <table class="w-100 border">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Статус</th>
                    <th>Дата создания</th>
                    <th>Дата изменения последнего статуса</th>
                    <th>Потрачено времени</th>
                    <th>Кол-во позиций</th>
                    <th>Курьер</th>
                    <th>Тип заказа</th>
                    <th>Сумма</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    @php($clientInfo = json_decode($order->client_raw_data))
                    @php($productsModificationsInOrder = \App\Http\Controllers\Orders\OrdersController::OrderProductsModifications($order))
                    @php($rawData = json_decode($order->all_information_raw_data))
                    @php($sum += $rawData->orderSum)
                    @if($clientInfo->typePayment[0] === false)
                        @php($sumCash += $rawData->orderSum)

                        @php(empty($amountOrdersInDays[$order->created_at->format('Ymd')]['cash']) ? $amountOrdersInDays[$order->created_at->format('Ymd')]['cash'] = 0 : "")
                        @php($amountOrdersInDays[$order->created_at->format('Ymd')]['cash'] += 1)

                        @php(empty($sumOrdersInDays[$order->created_at->format('Ymd')]['cash']) ? $sumOrdersInDays[$order->created_at->format('Ymd')]['cash'] = 0 : "")
                        @php($sumOrdersInDays[$order->created_at->format('Ymd')]['cash'] += $rawData->orderSum)
                    @else
                        @php($sumBank += $rawData->orderSum)

                        @php(empty($amountOrdersInDays[$order->created_at->format('Ymd')]['bank']) ? $amountOrdersInDays[$order->created_at->format('Ymd')]['bank'] = 0 : "")
                        @php($amountOrdersInDays[$order->created_at->format('Ymd')]['bank'] += 1)

                        @php(empty($sumOrdersInDays[$order->created_at->format('Ymd')]['bank']) ? $sumOrdersInDays[$order->created_at->format('Ymd')]['bank'] = 0 : "")
                        @php($sumOrdersInDays[$order->created_at->format('Ymd')]['bank'] += $rawData->orderSum)
                    @endif

                    @if($order->Creator()->User->UserIsManager())
                        @php($orderCreator = 'Менеджер')
                        @php($ordersCreatorManager += 1)
                    @else
                        @php($orderCreator = 'Сайт')
                        @php($ordersCreatorWeb += 1)
                    @endif

                    <tr>
                        <td><a target="_blank" href="{{route('manager-arm-order-page', $order->id)}}">{{$order->id}}</a></td>
                        <td class="order-status-{{$order->status_id}}">{{\App\Models\Orders::STATUS[$order->status_id]}}</td>
                        <td>{{$order->created_at}}</td>
                        <td>{{$order->updated_at}}</td>
                        <td>{{date_diff($order->created_at, $order->updated_at)->format('%H:%I:%S')}}</td>
                        <td>{{$productsModificationsInOrder->count()}}</td>
                        <td>{{$order->courier_id}}</td>
                        <td>{{$orderCreator}}</td>
                        <td>{{$rawData->orderSum}}</td>
                        <td class="text-center">
                            <div class="order-detail-info">Подробно</div>
                            <div class="order-detail-info-content hide">
                                @foreach($productsModificationsInOrder as $productModificationInOrder)

                                    @php($costPrice = 0)
                                    @php($modificationIngredients = $productModificationInOrder->ProductModifications->Ingredients)
                                    @foreach($modificationIngredients as $ingredient)
                                        <?php
                                            $sumIngredient = $ingredient->ingredient_amount * $ingredient->Ingredient->CurrentPrice();
                                            $costPrice += $sumIngredient;
                                        ?>
                                    @endforeach
                                    @php($sumCost += $costPrice)

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

        <div style="order: 1;">
            <div class="mb-10">Итого: {{$sum}} (Наличные: {{$sumCash}} / Банк: {{$sumBank}})</div>
            <div class="mb-10">Себестоимость: {{$sumCost}}</div>
            <div class="mb-10">Кол-во заказов: {{$orders->count()}} (Сайт: {{$ordersCreatorWeb}} / Менеджер {{$ordersCreatorManager}})</div>
            <div class="mb-10">Средний чек: {{$sum / $orders->count()}}</div>
            <div class="mb-10">
                <div class="toggle-button cp" data-toogle="amount-orders-in-days-container">Кол-во заказов в день (нал/банк/всего) (нажать. раскроется.)</div>
                <div class="amount-orders-in-days-container">
                    @foreach($amountOrdersInDays as $date => $amountOrdersInDay)
                        <div>Дата: {{date('Y-m-d', strtotime($date))}} - Кол-во: {{$amountOrdersInDay['cash']}}/{{$amountOrdersInDay['bank']}}/{{$amountOrdersInDay['cash'] + $amountOrdersInDay['bank']}} - сумма: {{$sumOrdersInDays[$date]['cash']}}/{{$sumOrdersInDays[$date]['bank']}}/{{$sumOrdersInDays[$date]['cash'] + $sumOrdersInDays[$date]['bank']}}</div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>


@stop

@section('js')

    <script>
        function sortTable(table, col, reverse) {
            var tb = table.tBodies[0], // use `<tbody>` to ignore `<thead>` and `<tfoot>` rows
                tr = Array.prototype.slice.call(tb.rows, 0), // put rows into array
                i;
            reverse = -((+reverse) || -1);
            tr = tr.sort(function (a, b) { // sort rows
                return reverse // `-1 *` if want opposite order
                    * (a.cells[col].textContent.trim() // using `.textContent.trim()` for test
                            .localeCompare(b.cells[col].textContent.trim())
                    );
            });
            for(i = 0; i < tr.length; ++i) tb.appendChild(tr[i]); // append each row in order
        }

        function makeSortable(table) {
            var th = table.tHead, i;
            th && (th = th.rows[0]) && (th = th.cells);
            if (th) i = th.length;
            else return; // if no `<thead>` then do nothing
            while (--i >= 0) (function (i) {
                var dir = 1;
                th[i].addEventListener('click', function () {sortTable(table, i, (dir = 1 - dir))});
            }(i));
        }

        function makeAllSortable(parent) {
            parent = parent || document.body;
            var t = parent.getElementsByTagName('table'), i = t.length;
            while (--i >= 0) makeSortable(t[i]);
        }

        makeAllSortable();
    </script>

    <script>
        let orderDetailInfoButtons = document.body.querySelectorAll('.order-detail-info');
        orderDetailInfoButtons.forEach((orderDetailInfoButton) => {
            orderDetailInfoButton.addEventListener('click', (event) => {
                let orderDetailInfoContent = event.target.nextElementSibling.innerHTML;
                let modal = ModalWindow(orderDetailInfoContent);
                modal.querySelector('.order-detail-info-content').show();
            });
        });

        ToggleShow();
    </script>

@stop
