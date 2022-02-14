@extends('app')

@section('content')

    <style>

        @media screen and (max-width: 540px) {
            .start-date,
            .end-date {
                width: 100%;
                margin-bottom: 5px;
            }
        }

        .is-bad-job {
            background-color: #e37e7e;
        }

    </style>

    <div class="mb-10">
        <a class="orange-button" href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div class="flex-wrap mb-10">
        <div class="mr-10 flex-center">
            <label>
                <span>Период</span>
                <input class="start-date" type="date" value="{{$startDate}}">
                <input class="end-date" type="date" value="{{$endDate}}">
            </label>
        </div>
    </div>

    <div class="flex-column">

        <div>

            <div class="mb-10">

                <table>
                    <tbody>
                    <tr class="hover-color">
                        <td class="text-center">Итого</td>
                        <td class="text-center">{{number_format($ordersStatistics->ordersAmount, 0, '.', '\'')}} ₽</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Наличные</td>
                        <td class="text-center">{{number_format($ordersStatistics->ordersAmountCash, 0, '.', '\'')}} ₽</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Банк</td>
                        <td class="text-center">{{number_format($ordersStatistics->ordersAmountBank, 0, '.', '\'')}} ₽</td>
                    </tr>
                    </tbody>
                </table>

            </div>

            <div class="mb-10">
                <table>
                    <tbody>
                    <tr class="hover-color">
                        <td class="text-center">Кол-во дней</td>
                        <td class="text-center">{{$dayCount}}</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Кол-во заказов</td>
                        <td class="text-center">{{$ordersStatistics->ordersQuantity}}</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Кол-во заказов с сайта</td>
                        <td class="text-center">{{$ordersStatistics->ordersCreatorWeb}}</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Кол-во заказов от менеджера</td>
                        <td class="text-center">{{$ordersStatistics->ordersCreatorManager}}</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Кол-во заказов от собственника</td>
                        <td class="text-center">{{$ordersStatistics->ordersCreatorAdmin}}</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Кол-во отказов</td>
                        <td class="text-center">{{$ordersStatistics->amountOrdersCancelled}}</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Среднее кол-во заказов в день</td>
                        <td class="text-center">{{ number_format( ($ordersStatistics->ordersQuantity / $dayCount) , 2, '.', '\'')}}</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Средний чек</td>
                        <td class="text-center">{{ number_format( ($ordersStatistics->middleInvoice) , 2, '.', '\'') }} ₽</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Средний чек без самовывоза</td>
                        <td class="text-center">{{ number_format( ($ordersStatistics->middleInvoiceNotDelivery) , 2, '.', '\'') }} ₽</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Прибыль</td>
                        <td class="text-center">{{number_format($ordersStatistics->ordersMarginAmount, 2, '.', '\'')}} ₽</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Себестоимость</td>
                        <td class="text-center">{{number_format($ordersStatistics->ordersCostAmount, 2, '.', '\'')}} ₽</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Поставки</td>
                        <td class="text-center">{{number_format($supplySum, 2, '.', '\'')}} ₽</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Прогноз на 30 дней в ₽</td>
                        <td class="text-center">{{number_format(($ordersStatistics->ordersMarginAmount / $dayCount * 30), 2, '.', '\'')}} ₽</td>
                    </tr>
                    <tr class="hover-color">
                        <td class="text-center">Прогноз на 30 дней в заказах</td>
                        <td class="text-center">{{number_format(($ordersStatistics->ordersQuantity / $dayCount * 30), 0, '.', '\'')}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-10">
                <div class="toggle-button cp" data-toogle="amount-orders-in-days-container">Кол-во заказов по дням</div>
                <div class="amount-orders-in-days-container mt-10">

                    <table id="amount-orders-in-days-table">
                        <thead>
                        <tr>
                            <th>Дата</th>
                            <th>День недели</th>
                            <th>Кол-во нал</th>
                            <th>Кол-во банк</th>
                            <th>Всего</th>
                            <th>Средний чек</th>
                            <th>Сумма нал</th>
                            <th>Сумма банк</th>
                            <th>Всего</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ordersStatistics->ordersAmountInDays as $date => $values)
                            <tr class="hover-color">
                                <td class="text-center">{{$date}}</td>
                                <td class="text-center">{{$values['dayOfWeek']}}</td>
                                <td class="text-center">{{$values['ordersNumberCash']}}</td>
                                <td class="text-center">{{$values['ordersNumberBank']}}</td>
                                <td class="text-center">{{$values['ordersNumberCash'] + $values['ordersNumberBank']}}</td>
                                <td class="text-center">{{number_format($values['middleInvoice'], 2, '.', '\'')}} ₽</td>
                                <td class="text-center">{{number_format($values['ordersAmountCash'], 0, '.', '\'')}} ₽</td>
                                <td class="text-center">{{number_format($values['ordersAmountBank'], 0, '.', '\'')}} ₽</td>
                                <td class="text-center">{{number_format($values['ordersAmountCash'] + $values['ordersAmountBank'], 0, '.', '\'')}} ₽</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="cp" onclick="fnExcelReport('amount-orders-in-days-table')" >Скачать</div>

                </div>
            </div>

            <div class="mb-10">
                <div class="toggle-button cp" data-toogle="orders-by-couriers">Распределение по курьерам</div>
                <div class="orders-by-couriers mt-10">

                    <table>
                        <thead>
                        <tr>
                            <th>Имя</th>
                            <th>Телефон</th>
                            <th>Кол-во заказов</th>
                            <th>Сумма</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($sumCourier = 0)
                        @foreach($ordersStatistics->ordersByCouriers as $courier)
                            <tr class="hover-color">
                                <td>{{$courier['name']}}</td>
                                <td>{{$courier['phone']}}</td>
                                <td class="text-center">{{$courier['orderAmount']}}</td>
                                <td class="text-center">{{number_format($courier['orderAmount'] * 100, 0, '.', '\'')}} ₽</td>
                            </tr>
                            @php($sumCourier += $courier['orderAmount'] * 100)
                        @endforeach
                        </tbody>
                    </table>

                    <div>Итого: {{$sumCourier}} ₽</div>

                </div>
            </div>

        </div>

        <div class="mb-10">
            <div class="toggle-button cp" data-toogle="toggle-buttons-container">Фильтр колонок</div>
            <div>
                <div class="toggle-buttons-container"></div>
            </div>
        </div>

        <div>
            <table class="w-100 border table-sort" id="orders-report" style="width: max-content;">
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
                    <th data-title-column-id="12" class="table-columns w-0">Выполнен (Деньги в кассе)</th>
                    <th data-title-column-id="13" class="table-columns w-0">Кол-во позиций</th>
                    <th data-title-column-id="14" class="table-columns w-0">Курьер</th>
                    <th data-title-column-id="15" class="table-columns w-0">Тип доставки</th>
                    <th data-title-column-id="16" class="table-columns w-0">Номер заказавшего</th>
                    <th data-title-column-id="17" class="table-columns w-0">Кол-во заказов у клиента</th>
                    <th data-title-column-id="18" class="table-columns ">Адрес</th>
                    <th data-title-column-id="19" class="table-columns ">Комментарий</th>
                    <th data-title-column-id="20" class="table-columns w-0">Откуда заказ</th>
                    <th data-title-column-id="21" class="table-columns w-0">Тип оплаты</th>
                    <th data-title-column-id="22" class="table-columns w-0">Сумма</th>
                    <th data-title-column-id="23" class="table-columns w-0">Себестоимость заказа</th>
                    <th data-title-column-id="24" class="table-columns w-0">Прибыль</th>
                    <th data-title-column-id="25" class="table-columns w-0">Подробнее</th>
                </tr>
                </thead>
                <tbody>
                <?php /** @var \App\Services\Orders\Order $order */ ?>
                @foreach($orders as $order)
                    <tr class="order hover-color">
                        <td data-column-id="1" class="text-center"><a target="_blank" href="{{route('manager-arm-order-page', $order->id)}}">{{$order->id}}</a></td>
                        <td data-column-id="2" class="order-status-{{$order->statusId}}">{{$order->statusText}}</td>
                        <td data-column-id="3">{{$order->createdAt}}</td>
                        <td data-column-id="4">{{$order->statuses->lastStatusTime}}</td>
                        <td data-column-id="5" class="text-center @if($order->isLongTime) is-bad-job @endif">{{$order->orderTime}}</td>
                        <td data-column-id="6" class="text-center @if($order->isLongTimeCookedToDelivered) is-bad-job @endif">{{$order->timeCookedToDelivered}}</td>
                        <td data-column-id="7" class="text-center">{{$order->statuses->timeManagerProcesses}}</td>
                        <td data-column-id="8" class="text-center">{{$order->statuses->timeTransferOnKitchen}}</td>
                        <td data-column-id="9" class="text-center">{{$order->statuses->timeCooked}}</td>
                        <td data-column-id="10" class="text-center">{{$order->statuses->timeCourier}}</td>
                        <td data-column-id="11" class="text-center">{{$order->statuses->timeDelivered}}</td>
                        <td data-column-id="12" class="text-center">{{$order->statuses->timeCompleted}}</td>
                        <td data-column-id="13" class="text-center">{{$order->productsAmount}}</td>
                        <td data-column-id="14">({{$order->courierId}})&nbsp;{{$order->courierName}}</td>
                        <td data-column-id="15" class="text-center">{{$order->clientInfo->typeDeliveryText}}</td>
                        <td data-column-id="16"><a target="_blank" href="{{route('administrator-arm-user-orders-page', $order->clientInfo->userId)}}">{{$order->clientInfo->clientPhone}}</a></td>
                        <td data-column-id="17" class="text-center">{{$order->clientInfo->ordersCount}}</td>
                        <td data-column-id="18">{{$order->clientInfo->clientAddressDelivery}}</td>
                        <td data-column-id="19">{{$order->clientInfo->clientComment}}</td>
                        <td data-column-id="20" class="text-center">{{$order->creatorType}}</td>
                        <td data-column-id="21" class="text-center">{{$order->clientInfo->typePaymentText}}</td>
                        <td data-column-id="22" class="text-center">{{$order->amount}}</td>
                        <td data-column-id="23" class="text-center">{{number_format($order->cost, 2, '.', '')}}</td>
                        <td data-column-id="24" class="text-center">{{number_format($order->margin, 2, '.', '')}}</td>
                        <td data-column-id="25" class="text-center">
                            <div class="order-detail-info cp">Подробно</div>
                            <div class="order-detail-info-content hide">

                                <table class="white-border">
                                    <thead>
                                    <tr>
                                        <th>Статус</th>
                                        <th>Название продукта</th>
                                        <th>Кол-во</th>
                                        <th>Цена</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php /** @var \App\Services\Orders\Products $product */ ?>
                                    @foreach($order->products as $product)
                                        <tr>
                                            <td class="product-in-order-status-{{$product->statusId}}">{{$product->statusText}}</td>
                                            <td>{{$product->title}}</td>
                                            <td class="text-center">{{$product->amount}}</td>
                                            <td class="text-center">{{$product->sellingPrice}} ₽</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>

            <div class="cp" onclick="fnExcelReport('orders-report')" >Скачать</div>

        </div>

    </div>

    <div class="w-100 scroll-x-auto">
        <canvas id="canvas" width="1350" height="520"></canvas>
    </div>

@stop

@section('js')

    <script>
        let orderDetailInfoButtons = document.body.querySelectorAll('.order-detail-info');
        orderDetailInfoButtons.forEach((orderDetailInfoButton) => {
            orderDetailInfoButton.addEventListener('click', (event) => {
                let orderDetailInfoContent = event.target.nextElementSibling.innerHTML;
                ModalWindow(orderDetailInfoContent);
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

        let data = JSON.parse('{!! json_encode($ordersStatistics->ordersNumberInHour) !!}');

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

            let columnsInOrder = localStorage.getItem('columnsInOrder');
            let isChecked;
            if (columnsInOrder !== null) {
                columnsInOrder = JSON.parse(columnsInOrder);
                if (columnsInOrder['column-id-' + columnId] !== undefined) {
                    isChecked = columnsInOrder['column-id-' + columnId] ? ' checked ' : '';
                } else {
                    isChecked = ' checked ';
                }
            } else {
                isChecked = ' checked ';
            }

            let content =
                '<label class="custom-checkbox-label" for="column-title-id-'+columnId+'">' +
                    '<input class="change-visible-column" data-toggler-column-id="" type="checkbox" id="column-title-id-'+columnId+'" ' + isChecked + ' />' +
                    '<div class="custom-checkbox-slider round"></div>' +
                '</label>' +
                '<span class="ml-5">'+columnTitle.innerHTML+'</span>';

            let toggleButton = CreateElement('div', {class: 'flex-center-vertical mb-5', content: content});

            toggleButtonContainer.append(toggleButton);

            if (isChecked === ' checked ') {
                document.body.querySelector('th[data-title-column-id="' + columnId + '"]').show();
                document.body.querySelectorAll('td[data-column-id="' + columnId + '"]').forEach((column) => {
                    column.show();
                });
            } else {
                document.body.querySelector('th[data-title-column-id="' + columnId + '"]').hide();
                document.body.querySelectorAll('td[data-column-id="' + columnId + '"]').forEach((column) => {
                    column.hide();
                });
            }

            toggleButton.addEventListener('change', (event) => {

                document.body.querySelector('th[data-title-column-id="' + columnId + '"]').showToggle();
                document.body.querySelectorAll('td[data-column-id="' + columnId + '"]').forEach((column) => {
                    column.showToggle();
                });

                let columnsInOrder = localStorage.getItem('columnsInOrder');
                if (columnsInOrder !== null) {
                    columnsInOrder = JSON.parse(columnsInOrder);
                } else {
                    columnsInOrder = {};
                }

                columnsInOrder['column-id-' + columnId] = event.target.checked;
                localStorage.setItem('columnsInOrder', JSON.stringify(columnsInOrder));
            });
        });

        function fnExcelReport(tableId) {
            var tab_text = "<table border='2px'><tr bgcolor='#FFFFFF'>";
            var j = 0;
            tab = document.getElementById(tableId);

            for (j = 1; j < tab.rows.length; j++) {
                tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
            }

            tab_text = tab_text + "</table>";
            tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");
            tab_text = tab_text.replace(/<img[^>]*>/gi, "");
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, "");

            var uri = 'data:application/vnd.ms-excel,';
            var a = document.createElement('a');
            a.setAttribute("href", uri + encodeURIComponent(tab_text))
            a.setAttribute('download', new Date() + '.xls');
            document.body.appendChild(a);
            a.click()
        }

    </script>

@stop
