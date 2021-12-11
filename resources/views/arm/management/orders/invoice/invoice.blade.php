<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            padding: 0;
            margin: 0;
        }
        .w-100 {
            width: 100%;
        }
        .flex-center {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .flex-column-center {
            display: flex;
            align-items: center;
            flex-direction: column;
        }
        .text-right {
            text-align: right;
        }
        .mb-10 {
            margin-bottom: 10px;
        }
        .mb-5 {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
<div id="print" style="width: 70mm;">
    <div class="flex-center">
        <div style="width: 30%;">
            <img class="w-100" src="{{url('invoice/logo-min.png')}}" alt="">
        </div>
    </div>
    <div class="flex-column-center">
        <div class="w-100 mb-10 flex-column-center">
            <div style="font-weight: 600;">BROпицца</div>
            <div>+7(926)585-36-21</div>
            <div>г.Дубна, ул Вернова, д 9</div>
        </div>
        <div class="w-100 mb-5" style="font-style: italic; padding-left: 10px;">Заказ:</div>

        @php($orderSumOriginal = 0)
        @foreach($productsModificationsInOrder as $productModificationInOrder)
            @php($orderSumOriginal += ($productModificationInOrder->ProductModifications->selling_price * $productModificationInOrder->product_modification_amount))
            <div class="w-100 mb-10">
                <div>{{$loop->iteration . '. ' . $productModificationInOrder->ProductModifications->Product->title . ' ' . ($productModificationInOrder->ProductModifications->Modification->title !== 'Соло-продукт' ? $productModificationInOrder->ProductModifications->Modification->title . ' ' . $productModificationInOrder->ProductModifications->Modification->value : '')}}</div>
                <div class="w-100 text-right">{{$productModificationInOrder->ProductModifications->selling_price}} ₽ * {{$productModificationInOrder->product_modification_amount}} шт</div>
            </div>
        @endforeach

        <div class="w-100 mb-10">
            <div class="w-100 text-right">Сумма заказа: {{$orderSumOriginal}} ₽</div>
            @if(($orderSumOriginal - $rawData->orderSum) > 0)
                <div class="w-100 text-right">Скидка: {{$orderSumOriginal - $rawData->orderSum}} ₽</div>
            @endif
            <div class="w-100 text-right">Итого: {{$rawData->orderSum}} ₽</div>
        </div>
        <div class="w-100 mb-10">
            <div class="w-100 mb-5" style="font-style: italic; padding-left: 10px;">Информация о клиенте:</div>
            <div class="w-100">{{$clientInfo->clientName}}</div>
            <div class="w-100">{{\App\Helpers\StringHelper::PhoneBeautifulFormat($clientInfo->clientPhone)}}</div>
            <div class="w-100">{{$clientInfo->clientAddressDelivery}}</div>
            <div class="w-100">{{$clientInfo->clientComment}}</div>
            <div class="w-100">{{($clientInfo->typePayment[0] === true ? 'Карта' : 'Наличные')}}</div>
        </div>
        <div>Приятного аппетита!</div>
        <div>Ваша BROпицца</div>
    </div>
    <div class="flex-center">
        <div style="width: 40%">
            <img class="w-100" src="{{url('invoice/qr.png')}}" alt="">
        </div>
        <div>
            <div>PIZZA-DUBNA.RU</div>
            <div>vk.com/bropizza_dubna</div>
            <div>insta: bro_pizza_dubna</div>
        </div>
    </div>
</div>
<script>
    window.print();
    setTimeout(() => {
        window.close();
    }, 500);
</script>
</body>
</html>
