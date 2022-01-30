@extends('app')

@section('content')

    <style>
        .order:hover {
            background-color: wheat;
        }

        @media screen and (max-width: 540px) {
            .start-date,
            .end-date {
                width: 100%;
                margin-bottom: 5px;
            }
        }

    </style>

    <div class="mb-10">
        <a class="orange-button" href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div class="flex-wrap mb-10">
        <div class="mr-10 flex-center">
            <label>
                <span>На какое число</span>
                <input class="start-date" type="date" value="{{$startDate}}">
                <input class="end-date" type="date" value="{{$endDate}}">
            </label>
        </div>
        <button class="orange-button all-orders mr-10">Заказы за всё время</button>
        <button class="orange-button all-orders-today">Заказы за сегодня</button>
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
    @php($ordersByCouriers = [])

    <div class="flex-column">

        <div style="order: 2;">
            <div class="toggle-button cp" data-toogle="toggle-buttons-container">Показать фильтр колонок</div>
            <div>
                <div class="toggle-buttons-container"></div>
            </div>
        </div>

        <div style="order: 3;">
            <table class="w-100 border table-sort" style="width: max-content;">
                <thead>
                <tr>
                    <th data-title-column-id="1" class="table-columns w-0">ID</th>
                    <th data-title-column-id="2" class="table-columns ">Статус</th>
                    <th data-title-column-id="3" class="table-columns ">Дата создания</th>
                    <th data-title-column-id="4" class="table-columns ">Дата изменения последнего статуса</th>
                    <th data-title-column-id="5" class="table-columns w-0">Потрачено времени всего</th>
                    <th data-title-column-id="6" class="table-columns w-0">Передан на кухню -> Доставлен</th>
                    <th data-title-column-id="7" class="table-columns w-0">Менеджер зашел в заказ</th>
                    <th data-title-column-id="8" class="table-columns w-0">Менеджер передал на кухню</th>
                    <th data-title-column-id="9" class="table-columns w-0">Кухня приготовила</th>
                    <th data-title-column-id="10" class="table-columns w-0">Передан в доставку</th>
                    <th data-title-column-id="11" class="table-columns w-0">Доставлен</th>
                    <th data-title-column-id="12" class="table-columns w-0">Деньги в кассе</th>
                    <th data-title-column-id="13" class="table-columns w-0">Кол-во позиций</th>
                    <th data-title-column-id="14" class="table-columns w-0">Курьер</th>
                    <th data-title-column-id="15" class="table-columns w-0">Номер заказавшего</th>
                    <th data-title-column-id="16" class="table-columns ">Комментарий</th>
                    <th data-title-column-id="17" class="table-columns w-0">Тип заказа</th>
                    <th data-title-column-id="18" class="table-columns w-0">Сумма</th>
                    <th data-title-column-id="19" class="table-columns w-0">Подробнее</th>
                    <th data-title-column-id="20" class="table-columns w-0">Себестоимость заказа</th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    <?php /** @var \App\Models\Orders $order */ ?>

                    @php($clientInfo = json_decode($order->client_raw_data))
                    @php($productsModificationsInOrder = $order->ProductsModifications)
                    @php($rawData = json_decode($order->all_information_raw_data))
                    @php($longTime = false)
                    @php($longTimeDelivered = false)

                    @if($order->IsCancelled())
                        @php($amountOrdersCancelled++)
                    @else
                        @php($sum += $order->order_amount)
                        @php(empty($sumOrdersInHour[(int)$order->created_at->format('H')]) ? $sumOrdersInHour[(int)$order->created_at->format('H')] = 1 : $sumOrdersInHour[(int)$order->created_at->format('H')] += 1)
                        @if($clientInfo->typePayment[0] === false)
                            @php($sumCash += $order->order_amount)

                            @php(empty($amountOrdersInDays[$order->created_at->format('Ymd')]['cash']) ? $amountOrdersInDays[$order->created_at->format('Ymd')]['cash'] = 0 : "")
                            @php($amountOrdersInDays[$order->created_at->format('Ymd')]['cash'] += 1)

                            @php(empty($sumOrdersInDays[$order->created_at->format('Ymd')]['cash']) ? $sumOrdersInDays[$order->created_at->format('Ymd')]['cash'] = 0 : "")
                            @php($sumOrdersInDays[$order->created_at->format('Ymd')]['cash'] += $order->order_amount)
                        @else
                            @php($sumBank += $order->order_amount)

                            @php(empty($amountOrdersInDays[$order->created_at->format('Ymd')]['bank']) ? $amountOrdersInDays[$order->created_at->format('Ymd')]['bank'] = 0 : "")
                            @php($amountOrdersInDays[$order->created_at->format('Ymd')]['bank'] += 1)

                            @php(empty($sumOrdersInDays[$order->created_at->format('Ymd')]['bank']) ? $sumOrdersInDays[$order->created_at->format('Ymd')]['bank'] = 0 : "")
                            @php($sumOrdersInDays[$order->created_at->format('Ymd')]['bank'] += $order->order_amount)
                        @endif

                        @if(isset($order->courier_id) && $order->courier_id !== 0)
                            @php(empty($ordersByCouriers[$order->courier_id]) ? $ordersByCouriers[$order->courier_id] = 0 : "")
                            @php($ordersByCouriers[$order->courier_id] += 1)
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

                    @if(date_diff($order->created_at, $order->LatestStatus->updated_at)->format('%H') !== '00')
                        @php($longTime = true)
                    @endif

                    @if(date('H', strtotime(\App\Models\Orders::TimeBetweenStatuses($order->id, \App\Models\Orders::STATUS_TEXT['kitchen'], \App\Models\Orders::STATUS_TEXT['delivered']))) !== '00')
                        @php($longTimeDelivered = true)
                    @endif

                    <tr class="order">
                        <td data-column-id="1"><a target="_blank"
                                                  href="{{route('manager-arm-order-page', $order->id)}}">{{$order->id}}</a>
                        </td>
                        <td data-column-id="2"
                            class="order-status-{{$order->status_id}}">{{\App\Models\Orders::STATUS[$order->status_id]}}</td>
                        <td data-column-id="3">{{$order->created_at}}</td>
                        <td data-column-id="4">{{$order->LatestStatus->updated_at}}</td>
                        <td data-column-id="5" class="text-center"
                            @if($longTime) style="background-color: #e37e7e;" @endif>{{date_diff($order->created_at, $order->LatestStatus->updated_at)->format('%H:%I:%S')}}</td>
                        <td data-column-id="6" class="text-center"
                            @if($longTimeDelivered) style="background-color: #e37e7e;" @endif>{{\App\Models\Orders::TimeBetweenStatuses($order->id, \App\Models\Orders::STATUS_TEXT['kitchen'], \App\Models\Orders::STATUS_TEXT['delivered'])}}</td>
                        <td data-column-id="7" class="text-center">{{$order->TimeManagerProcesses()}}</td>
                        <td data-column-id="8" class="text-center">{{$order->TimeTransferOnKitchen()}}</td>
                        <td data-column-id="9" class="text-center">{{$order->TimeCooked()}}</td>
                        <td data-column-id="10" class="text-center">{{$order->TimeCourier()}}</td>
                        <td data-column-id="11" class="text-center">{{$order->TimeDelivered()}}</td>
                        <td data-column-id="12" class="text-center">{{$order->TimeCompleted()}}</td>
                        <td data-column-id="13" class="text-center">{{$productsModificationsInOrder->count()}}</td>
                        <td data-column-id="14">{{$order->courier_id}}
                            &nbsp;{{isset($order->Courier) ? '('.$order->Courier->name.')' : ''}}</td>
                        <td data-column-id="15">{{$order->User->phone}}</td>
                        <td data-column-id="16">{{$clientInfo->clientComment}}</td>
                        <td data-column-id="17" class="text-center">{{$orderCreator}}</td>
                        <td data-column-id="18" class="text-center">{{$order->order_amount}}</td>
                        <td data-column-id="19" class="text-center">
                            <div class="order-detail-info">Подробно</div>
                            <div class="order-detail-info-content hide">
                                @php($orderCost = 0)
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
                                        @php($orderCost += $costPrice * $productModificationInOrder->product_modification_amount)

                                    @endif

                                    @php($productsAndModificationsInOrderForOrderEdit[] = (object)['productId' => $productModificationInOrder->ProductModifications->Product->id, 'modificationId' => $productModificationInOrder->product_modification_id, 'amount' => $productModificationInOrder->product_modification_amount, 'modificationTypeId' => $productModificationInOrder->ProductModifications->Modification->type_id])
                                    <div class="p-5 mb-10 product-in-order-status-{{$productModificationInOrder->status_id}}">
                                        <div>{{\App\Models\ProductsModificationsInOrders::STATUS[$productModificationInOrder->status_id]}}</div>
                                        <div>{{$productModificationInOrder->ProductModifications->Product->title . ' ' . $productModificationInOrder->ProductModifications->Modification->title . ' ' . $productModificationInOrder->ProductModifications->Modification->value}}</div>
                                        <div>{{'Цена: ' . $productModificationInOrder->ProductModifications->selling_price . ' ₽'}}</div>
                                        <div>Кол-во: {{$productModificationInOrder->product_modification_amount}}</div>
                                    </div>
                                @endforeach
                                @php($sumCost += $orderCost)
                            </div>
                        </td>
                        <td data-column-id="20">{{$orderCost}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div style="order: 1;">
            <div class="mb-10">Итого: {{$sum}} (Наличные: {{$sumCash}} / Банк: {{$sumBank}})</div>
            <div class="mb-10">Себестоимость: {{$sumCost}}</div>
            <div class="mb-10">Поставки: {{$supplySum}}</div>
            <div class="mb-10">Кол-во заказов: {{$orders->count()}} (Сайт: {{$ordersCreatorWeb}} /
                Менеджер {{$ordersCreatorManager}} / Собственник {{$ordersCreatorAdmin}} /
                Отказ {{$amountOrdersCancelled}})
            </div>
            <div class="mb-10">Средний
                чек: {{($orders->count() - $amountOrdersCancelled) !== 0 ? ($sum / ($orders->count() - $amountOrdersCancelled)) : 0}}</div>
            <div class="mb-10">
                <div class="toggle-button cp" data-toogle="amount-orders-in-days-container">Кол-во заказов в день
                    (нал/банк/всего) (нажать. раскроется.)
                </div>
                <div class="amount-orders-in-days-container">
                    @foreach($amountOrdersInDays as $date => $amountOrdersInDay)
                        @php($amountOrdersInDayCash = $amountOrdersInDay['cash'] ?? 0)
                        @php($amountOrdersInDayBank = $amountOrdersInDay['bank'] ?? 0)
                        @php($amountOrdersInDayDateCash = $sumOrdersInDays[$date]['cash'] ?? 0)
                        @php($amountOrdersInDayDateBank = $sumOrdersInDays[$date]['bank'] ?? 0)
                        <div class="flex">
                            <div class="mr-10" style="width: 170px;">Дата: {{date('Y-m-d', strtotime($date))}}</div>
                            <div class="mr-10" style="width: 170px;">Кол-во: {{$amountOrdersInDayCash}}
                                /{{$amountOrdersInDayBank}}/{{$amountOrdersInDayCash + $amountOrdersInDayBank}}</div>
                            <div class="mr-10">Cумма: {{$amountOrdersInDayDateCash}} ₽ /{{$amountOrdersInDayDateBank}} ₽
                                /{{$amountOrdersInDayDateCash + $amountOrdersInDayDateBank}} ₽
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mb-10">
                <div class="toggle-button cp" data-toogle="orders-by-couriers">Распределение по курьерам</div>
                <div class="orders-by-couriers">
                    @php($sumCourier = 0)
                    @foreach($ordersByCouriers as $courierId => $amountOrder)
                        <div class="flex">
                            <div class="mr-10">{{\App\Models\User::find($courierId)->name}}</div>
                            <div class="mr-10">{{\App\Models\User::find($courierId)->phone}}</div>
                            <div class="mr-10">{{$amountOrder}} шт.</div>
                            <div class="mr-10">{{$amountOrder * 70}} ₽</div>
                            @php($sumCourier += $amountOrder * 70)
                        </div>
                    @endforeach
                    <div>Итого: {{$sumCourier}} ₽</div>
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

        let changeDateFields = document.body.querySelectorAll('.start-date, .end-date');
        changeDateFields.forEach((changeDateField) => {
            changeDateField.addEventListener('change', () => {
                LoaderShow();
                let startDate = document.body.querySelector('.start-date').value;
                let endDate = document.body.querySelector('.end-date').value;
                if (startDate && endDate) {
                    location.href = "{{route('administrator-arm-orders-page')}}?start-date=" + startDate + "&end-date=" + endDate;
                }
            });
        });

        let allOrdersTodayButton = document.body.querySelector('.all-orders-today');
        allOrdersTodayButton.addEventListener('click', () => {
            LoaderShow();
            location.href = "{{route('administrator-arm-orders-page')}}??start-date={{date('Y-m-d', time())}}&end-date={{date('Y-m-d', time())}}";
        });

        let allOrdersButton = document.body.querySelector('.all-orders');
        allOrdersButton.addEventListener('click', () => {
            LoaderShow();
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
        for (let i = 0; i <= 13; i++) {
            ctx.fillText(i + 10, ((i * 100)), 510);
        }

        let data = JSON.parse('{!! json_encode($sumOrdersInHour) !!}');

        // Назначаем зелёный цвет для графика
        ctx.fillStyle = "green";
        // Цикл для от рисовки графиков
        for (let i = 0; i < 23; i++) {
            let dp = data[i + 10];
            ctx.fillRect(i * 100, 500 - dp * 5, 5, dp * 5);
            ctx.fillText(data[i + 10], ((i * 100)), 490 - dp * 5);
        }

        const toggleButtonContainer = document.body.querySelector('.toggle-buttons-container');
        document.body.querySelectorAll('.table-columns').forEach((columnTitle) => {

            let columnId = columnTitle.dataset.titleColumnId;

            let content = '<label class="custom-checkbox-label" for="column-title-id-'+columnId+'">' +
                                '<input class="change-visible-column" data-toggler-column-id="" type="checkbox" id="column-title-id-'+columnId+'" checked />' +
                                '<div class="custom-checkbox-slider round"></div>' +
                            '</label>' +
                            '<span>'+columnTitle.innerHTML+'</span>';

            let toggleButton = CreateElement('div', {content: content});

            toggleButtonContainer.append(toggleButton);

            toggleButton.addEventListener('change', () => {
                document.body.querySelector('th[data-title-column-id="' + columnId + '"]').showToggle();
                document.body.querySelectorAll('td[data-column-id="' + columnId + '"]').forEach((column) => {
                    column.showToggle();
                });
            });
        });
    </script>

@stop
