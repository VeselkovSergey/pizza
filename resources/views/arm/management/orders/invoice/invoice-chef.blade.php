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
    </style>
</head>
<body>
<div id="print" style="width: 70mm;">
    <div>*</div>
    <div>*</div>
    <div>*</div>
    <div class="flex-column-center">
        <div class="mb-10" style="font-weight: 600;"># {{$order->id}} {{date('Y-m-d H:i:s', time())}}</div>
        @foreach($productsModificationsInOrder as $productModificationInOrder)
            <div class="w-100 mb-10" style="font-size: 20px">
                <div>{{$loop->iteration . '. ' . $productModificationInOrder->ProductModifications->Product->title . ' ' . ($productModificationInOrder->ProductModifications->Modification->title !== 'Соло-продукт' ? $productModificationInOrder->ProductModifications->Modification->title . ' ' . $productModificationInOrder->ProductModifications->Modification->value : '')}}</div>
                <div class="w-100 text-right">{{$productModificationInOrder->product_modification_amount}} шт</div>
            </div>
        @endforeach
    </div>
    <div>*</div>
    <div>*</div>
    <div>*</div>
</div>
<script>
    window.print();
    setTimeout(() => {
        window.close();
    }, 500);
</script>
</body>
</html>
