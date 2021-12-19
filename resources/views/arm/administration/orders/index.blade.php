@extends('app')

@section('content')

    <style>
        .order:hover {
            background-color: wheat;
        }
    </style>

    <div class="mb-10">
        <a href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div class="flex mb-10">
        <div class="mr-10">
            <label>
                На какое число
                <input class="required-date" type="date" value="{{$requiredDate}}">
            </label>
        </div>
        <button class="cp all-orders mr-10">Заказы за всё время</button>
        <button class="cp all-orders-today">Заказы за сегодня</button>
    </div>

    @php($sum = 0)
    @php($sumCash = 0)
    @php($sumBank = 0)
    @php($sumCost = 0)
    @php($ordersCreatorWeb = 0)
    @php($ordersCreatorManager = 0)
    @php($ordersCreatorAdmin = 0)
    @php($amountOrdersCancelled = 0)
    @php($amountOrdersInDays = [])
    @php($sumOrdersInDays = [])
    @php($sumOrdersInHour = [])

    <div class="flex-column">
        <div style="order: 2; overflow-x: auto;">
            <table class="w-100 border table-sort" style="width: max-content;">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Статус</th>
                    <th>Дата создания</th>
                    <th>Дата изменения последнего статуса</th>
                    <th>Потрачено времени всего</th>
                    <th>Менеджер зашел в заказ</th>
                    <th>Менеджер передал на кухню</th>
                    <th>Кухня приготовила</th>
                    <th>Передан в доставку</th>
                    <th>Доставлен</th>
                    <th>Деньги в кассе</th>
                    <th>Кол-во позиций</th>
                    <th>Курьер</th>
                    <th>Номер заказавшего</th>
                    <th>Комментарий</th>
                    <th>Тип заказа</th>
                    <th>Сумма</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    <?php /** @var \App\Models\Orders $order  */ ?>

                    @php($clientInfo = json_decode($order->client_raw_data))
                    @php($productsModificationsInOrder = \App\Http\Controllers\Orders\OrdersController::OrderProductsModifications($order))
                    @php($rawData = json_decode($order->all_information_raw_data))
                    @php($longTime = false)

                    @if($order->IsCancelled())
                        @php($amountOrdersCancelled++)
                    @else
                        @php($sum += $rawData->orderSum)
                        @php(empty($sumOrdersInHour[(int)$order->created_at->format('H')]) ? $sumOrdersInHour[(int)$order->created_at->format('H')] = 1 : $sumOrdersInHour[(int)$order->created_at->format('H')] += 1)
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
                    @endif

                    @if($order->Creator()->User->UserIsAdmin())
                        @php($orderCreator = 'Собственник')
                        @php($ordersCreatorAdmin++)
                    @elseif($order->Creator()->User->UserIsManager())
                        @php($orderCreator = 'Менеджер')
                        @php($ordersCreatorManager++)
                    @else
                        @php($orderCreator = 'Сайт')
                        @php($ordersCreatorWeb++)
                    @endif

                    @if(date_diff($order->created_at, $order->updated_at)->format('%H') !== '00')
                        @php($longTime = true)
                    @endif

                    <tr class="order">
                        <td><a target="_blank" href="{{route('manager-arm-order-page', $order->id)}}">{{$order->id}}</a></td>
                        <td class="order-status-{{$order->status_id}}">{{\App\Models\Orders::STATUS[$order->status_id]}}</td>
                        <td>{{$order->created_at}}</td>
                        <td>{{$order->updated_at}}</td>
                        <td @if($longTime) style="background-color: #e37e7e;" @endif>{{date_diff($order->created_at, $order->updated_at)->format('%H:%I:%S')}}</td>
                        <td>{{$order->TimeManagerProcesses()}}</td>
                        <td>{{$order->TimeTransferOnKitchen()}}</td>
                        <td>{{$order->TimeCooked()}}</td>
                        <td>{{$order->TimeCourier()}}</td>
                        <td>{{$order->TimeDelivered()}}</td>
                        <td>{{$order->TimeCompleted()}}</td>
                        <td>{{$productsModificationsInOrder->count()}}</td>
                        <td>{{$order->courier_id}} {{isset($order->Courier) ? '('.$order->Courier->name.')' : ''}}</td>
                        <td>{{$order->User->phone}}</td>
                        <td>{{$clientInfo->clientComment}}</td>
                        <td>{{$orderCreator}}</td>
                        <td>{{$rawData->orderSum}}</td>
                        <td class="text-center">
                            <div class="order-detail-info">Подробно</div>
                            <div class="order-detail-info-content hide">
                                @foreach($productsModificationsInOrder as $productModificationInOrder)

                                    @if(!$order->IsCancelled())

                                        @php($costPrice = 0)
                                        @php($modificationIngredients = $productModificationInOrder->ProductModifications->Ingredients)
                                        @foreach($modificationIngredients as $ingredient)
                                            <?php
                                            /** @var \App\Models\ProductModificationsIngredients $ingredient */
                                                $sumIngredient = $ingredient->ingredient_amount * $ingredient->Ingredient->CurrentPrice();
                                            ?>
                                                @php($costPrice += $sumIngredient)
                                        @endforeach
                                        @php($sumCost += $costPrice)

                                    @endif

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
            <div class="mb-10">Кол-во заказов: {{$orders->count()}} (Сайт: {{$ordersCreatorWeb}} / Менеджер {{$ordersCreatorManager}} / Собственник {{$ordersCreatorAdmin}} / Отказ {{$amountOrdersCancelled}})</div>
            <div class="mb-10">Средний чек: {{$orders->count() !== 0 ? ($sum / ($orders->count() - $amountOrdersCancelled)) : 0}}</div>
            <div class="mb-10">
                <div class="toggle-button cp" data-toogle="amount-orders-in-days-container">Кол-во заказов в день (нал/банк/всего) (нажать. раскроется.)</div>
                <div class="amount-orders-in-days-container">
                    @foreach($amountOrdersInDays as $date => $amountOrdersInDay)
                        @php($amountOrdersInDayCash = $amountOrdersInDay['cash'] ?? 0)
                        @php($amountOrdersInDayBank = $amountOrdersInDay['bank'] ?? 0)
                        @php($amountOrdersInDayDateCash = $sumOrdersInDays[$date]['cash'] ?? 0)
                        @php($amountOrdersInDayDateBank = $sumOrdersInDays[$date]['bank'] ?? 0)
                        <div>Дата: {{date('Y-m-d', strtotime($date))}} - Кол-во: {{$amountOrdersInDayCash}}/{{$amountOrdersInDayBank}}/{{$amountOrdersInDayCash + $amountOrdersInDayBank}} - сумма: {{$amountOrdersInDayDateCash}}/{{$amountOrdersInDayDateBank}}/{{$amountOrdersInDayDateCash + $amountOrdersInDayDateBank}}</div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <canvas id="canvas" width="1350" height="520"></canvas>


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

        ToggleShow();

        let changeRequiredDateInput = document.body.querySelector('.required-date');
        changeRequiredDateInput.addEventListener('change', (event) => {
            let requiredDate = event.target.value;
            if (requiredDate) {
                location.href = "{{route('administrator-arm-orders-page')}}?required-date=" + requiredDate;
            }
        });

        let allOrdersTodayButton = document.body.querySelector('.all-orders-today');
        allOrdersTodayButton.addEventListener('click', () => {
            location.href = "{{route('administrator-arm-orders-page')}}?required-date={{date('Y-m-d', time())}}";
        });

        let allOrdersButton = document.body.querySelector('.all-orders');
        allOrdersButton.addEventListener('click', () => {
            location.href = "{{route('administrator-arm-orders-page')}}?all=true";
        });

        // Получаем canvas элемент
        let canvas = document.getElementById('canvas');

        // Указываем элемент для 2D рисования
        let ctx = canvas.getContext('2d');

        ctx.fillStyle = "black"; // Задаём чёрный цвет для линий
        ctx.lineWidth = 1.0; // Ширина линии
        ctx.beginPath(); // Запускает путь
        ctx.moveTo(0, 500); // Указываем начальный путь
        // ctx.lineTo(0, 500); // Перемешаем указатель
        ctx.lineTo(1350, 500); // Ещё раз перемешаем указатель
        ctx.stroke(); // Делаем контур

        // Цвет для рисования
        ctx.fillStyle = "black";
        // Цикл для отображения значений по Y
        // for(let i = 0; i <= 20; i++) {
        //     ctx.fillText(i, 0, 500 - (i * 25));
        // }

        // Выводим меток
        for(let i = 0; i <= 13; i++) {
            ctx.fillText(i + 10, ((i * 100)), 510);
        }

        let data = JSON.parse('{!! json_encode($sumOrdersInHour) !!}');

        // Назначаем зелёный цвет для графика
        ctx.fillStyle = "green";
        // Цикл для от рисовки графиков
        for(let i = 0; i < 23; i++) {
            let dp = data[i+10];
            ctx.fillRect(i*100, 500-dp*5 , 5, dp*5);
            ctx.fillText(data[i+10], ((i * 100)), 490-dp*5);
        }
    </script>

@stop
