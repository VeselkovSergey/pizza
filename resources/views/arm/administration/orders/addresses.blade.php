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

    <div class="flex-column hide">

        @foreach($orders as $order)
            <?php /** @var \App\Models\Orders $order */ ?>

            @php($clientInfo = json_decode($order->client_raw_data))

            @continue($order->courier_id === 0)
            @continue($order->status_id === 9)

            <div class="order-block" data-order-id="{{$order->id}}" data-yandex-ok="{{isset($order->geo_yandex) ? 'true' : 'false'}}">
                <div class="order-address">{{$clientInfo->clientAddressDelivery}}</div>
                <div class="yandex-address-text">{{isset($order->geo_yandex) ? json_decode($order->geo_yandex)->addressText : ''}}</div>
                <div class="yandex-address-lat">{{isset($order->geo_yandex) ? json_decode($order->geo_yandex)->addressLat : ''}}</div>
                <div class="yandex-address-lon">{{isset($order->geo_yandex) ? json_decode($order->geo_yandex)->addressLon : ''}}</div>
            </div>

        @endforeach

    </div>

    <div id="map" class="w-100" style="height: calc(100vh - 250px)"></div>


@stop

@section('js')

    <script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    <script type="text/javascript">

        ymaps.ready(init);

        const APIKEY_YA = '00ad06f6-97e8-4b19-b114-304b35812efb';

        function init() {
            LoaderShow();
            let myMap = new ymaps.Map('map', {
                center: [56.734422, 37.162106],
                zoom: 14
            });

            myMap.controls.remove('searchControl')

            let objectManager = new ymaps.ObjectManager({
                // Чтобы метки начали кластеризоваться, выставляем опцию.
                clusterize: true,
                // ObjectManager принимает те же опции, что и кластеризатор.
                gridSize: 32,
                clusterDisableClickZoom: true
            });

            // Чтобы задать опции одиночным объектам и кластерам,
            // обратимся к дочерним коллекциям ObjectManager.
            objectManager.objects.options.set('preset', 'islands#greenDotIcon');
            objectManager.clusters.options.set('preset', 'islands#greenClusterIcons');
            myMap.geoObjects.add(objectManager);

            let counter = 0;
            let allData = [];
            let orders = document.body.querySelectorAll('.order-block');
            let orderCount = orders.length;
            if (counter === orderCount) {
                LoaderHide();
            }
            orders.forEach((order) => {
                let orderId = order.dataset.orderId;
                let yandexOk = order.dataset.yandexOk;
                let orderAddress = order.querySelector('.order-address').innerHTML;

                if (yandexOk === 'false') {
                    Ajax('https://geocode-maps.yandex.ru/1.x?apikey='+APIKEY_YA+'&format=json&results=1&geocode=Россия, Московская область, Дубна, ' + orderAddress).then((res) => {
                        let addressText = res.response.GeoObjectCollection.featureMember[0].GeoObject.metaDataProperty.GeocoderMetaData.Address.formatted;
                        let position = res.response.GeoObjectCollection.featureMember[0].GeoObject.Point.pos;
                        let coordinate = position.split(' ');

                        let yandexGeo = {
                            addressText: addressText,
                            addressLat: coordinate[0],
                            addressLon: coordinate[1],
                        }

                        allData.push({
                            type: 'Feature',
                            id: counter++,
                            geometry: {
                                type: 'Point',
                                coordinates: [yandexGeo.addressLon, yandexGeo.addressLat],
                            },
                            properties: {
                                balloonContent: yandexGeo.addressText
                            }
                        });

                        if (orderCount == counter) {
                            InitPoints();
                        }

                        Ajax("{{route('order-update-geo-yandex')}}", "POST", {orderId: orderId, yandexGeo: JSON.stringify(yandexGeo)});
                    });
                } else {
                    let orderAddressLat = order.querySelector('.yandex-address-lat').innerHTML;
                    let orderAddressLon = order.querySelector('.yandex-address-lon').innerHTML;
                    let orderAddressYandexText = order.querySelector('.yandex-address-text').innerHTML;

                    allData.push({
                        type: 'Feature',
                        id: counter++,
                        geometry: {
                            type: 'Point',
                            coordinates: [orderAddressLon, orderAddressLat],
                        },
                        properties: {
                            balloonContent: orderAddressYandexText
                        }
                    });

                    if (orderCount == counter) {
                        InitPoints();
                    }
                }
            });

            function InitPoints() {
                objectManager.add({
                    type: 'FeatureCollection',
                    features: allData,
                });
                LoaderHide();
            }
        }

    </script>

    <script>
        let changeDateFields = document.body.querySelectorAll('.start-date, .end-date');
        changeDateFields.forEach((changeDateField) => {
            changeDateField.addEventListener('change', () => {
                LoaderShow();
                let startDate = document.body.querySelector('.start-date').value;
                let endDate = document.body.querySelector('.end-date').value;
                if (startDate && endDate) {
                    location.href = "{{route('administrator-arm-orders-addresses-page')}}?start-date=" + startDate + "&end-date=" + endDate;
                }
            });
        });

        let allOrdersTodayButton = document.body.querySelector('.all-orders-today');
        allOrdersTodayButton.addEventListener('click', () => {
            LoaderShow();
            location.href = "{{route('administrator-arm-orders-addresses-page')}}??start-date={{date('Y-m-d', time())}}&end-date={{date('Y-m-d', time())}}";
        });

        let allOrdersButton = document.body.querySelector('.all-orders');
        allOrdersButton.addEventListener('click', () => {
            LoaderShow();
            location.href = "{{route('administrator-arm-orders-addresses-page')}}?all=true";
        });
    </script>

@stop
