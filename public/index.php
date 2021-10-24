<!DOCTYPE html>
<html>

<head>
    <title>Проверка при вводе адреса доставки</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style>
        html,
        body {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
            font-size: 13px;
            font-family: sans-serif;
            overflow: hidden;
        }

        #footer {
            width: 376px;
            background-color: #f2f2ef;
            padding: 12px;
        }

        #map {
            height: 376px;
            width: 480px;
            margin: 0px 12px 18px 12px;
            position: relative;
        }

        #messageHeader,
        #message,
        #route,
        #header {
            width: 376px;
            margin: 12px 10px 12px 12px;
        }

        #button {
            display: inline-block;
            margin-top: 10px;
            font-size: 11px;
            color: rgb(68, 68, 68);
            text-decoration: none;
            user-select: none;
            padding: .2em 0.6em;
            outline: none;
            border: 1px solid rgba(0, 0, 0, .1);
            border-radius: 2px;
            background: rgb(245, 245, 245) linear-gradient(#f4f4f4, #f1f1f1);
            transition: all .218s ease 0s;
            height: 28px;
            width: 74px;
        }

        #button:hover {
            color: rgb(24, 24, 24);
            border: 1px solid rgb(198, 198, 198);
            background: #f7f7f7 linear-gradient(#f7f7f7, #f1f1f1);
            box-shadow: 0 1px 2px rgba(0, 0, 0, .1);
        }

        #button:active {
            color: rgb(51, 51, 51);
            border: 1px solid rgb(204, 204, 204);
            background: rgb(238, 238, 238) linear-gradient(rgb(238, 238, 238), rgb(224, 224, 224));
            box-shadow: 0 1px 2px rgba(0, 0, 0, .1) inset;
        }

        .input {
            height: 18px;
            margin-top: 10px;
            margin-right: 10px;
            width: 277px;
            padding: 4px;
            border: 1px solid #999;
            border-radius: 3px;
            box-shadow: 0 0 1px 1px rgba(0, 0, 0, 0);
            transition: .17s linear;
        }

        .input:focus {
            outline: none;
            border: 1px solid #fdd734;
            box-shadow: 0 0 1px 1px #fdd734;
        }

        .input_error,
        .input_error:focus {
            outline: none;
            border: 1px solid #f33;
            box-shadow: 0 0 1px 1px #f33;
        }

        #notice {
            left: 22px;
            margin: 0px;
            color: #f33;
            display: none;
        }

    </style>
    <!--
        Укажите свой API-ключ. Тестовый ключ НЕ БУДЕТ работать на других сайтах.
        Получить ключ можно в Кабинете разработчика: https://developer.tech.yandex.ru/keys/
    -->
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=b34354ac-c2f4-4586-8631-e87724612a88" type="text/javascript"></script>
    <script src="https://yandex.st/jquery/2.2.3/jquery.min.js" type="text/javascript"></script>
</head>

<body>
<div id="header">
    <input type="text" id="suggest1" class="input" placeholder="Введите адрес отправления">
    <button type="submit" id="button1">Проверить</button>
    <p id="notice1">Адрес не найден</p>
    <input type="text" id="suggest2" class="input" placeholder="Введите адрес назначения">
    <button type="submit" id="button2">Проверить</button>
    <p id="notice2">Адрес не найден</p>
</div>
<div id="route">
    <button type="submit" id="button3">Построить маршрут</button>
    <p id="notice3">Недостаточно данных для построения маршрута</p>
</div>
<div id="map"></div>
<div id="footer">
    <div id="messageHeader1"></div>
    <div id="message1"></div>
    <div id="messageHeader2"></div>
    <div id="message2"></div>
</div>

<script>
    ymaps.ready(init);

    function init() {
        // Стоимость за километр.
        var DELIVERY_TARIFF = 20,
            // Минимальная стоимость.
            MINIMUM_COST = 500;
        // https://tech.yandex.ru/maps/jsbox/2.1/input_validation
        // Подключаем поисковые подсказки к полю ввода.
        var suggestView = new ymaps.SuggestView('suggest1'),
            suggestView = new ymaps.SuggestView('suggest2'),
            map, routePanelControl,
            addrFrom, addrTo;


        map = new ymaps.Map('map', {
            center: [55.75, 37.65],
            zoom: 9,
            controls: []
        });
        // Создадим панель маршрутизации.
        routePanelControl = new ymaps.control.RoutePanel({
            options: {
                // Добавим заголовок панели.
                showHeader: true,
                title: 'Расчёт доставки'
            }
        });
        var zoomControl = new ymaps.control.ZoomControl({
            options: {
                size: 'small',
                float: 'none',
                position: {
                    bottom: 145,
                    right: 10
                }
            }
        });
        // Пользователь сможет построить только автомобильный маршрут.
        routePanelControl.routePanel.options.set({
            types: {
                auto: true
            }
        });
        // Неизменяемые точки "откуда" и "куда"
        routePanelControl.routePanel.state.set({
            fromEnabled: false,
            toEnabled: false
        });

        map.controls.add(routePanelControl).add(zoomControl);


        // При клике по кнопке запускаем верификацию введёных данных и построение маршрута
        $('#button1').bind('click', function(e) {
            geocode('#suggest1');
        });
        $('#button2').bind('click', function(e) {
            geocode('#suggest2');
        });
        $('#button3').bind('click', function(e) {
            if (addrFrom && addrTo) {
                showRoute(addrFrom.getAddressLine(), addrTo.getAddressLine());
            } else {
                $('#notice3').css('display', 'block');
            }
        });

        function geocode(ctrl_id) {
            // Забираем запрос из поля ввода.
            var request = $(ctrl_id).val();
            // Геокодируем введённые данные.
            ymaps.geocode(request).then(function(res) {
                var obj = res.geoObjects.get(0),
                    error, hint;

                if (obj) {
                    // Об оценке точности ответа геокодера можно прочитать тут: https://tech.yandex.ru/maps/doc/geocoder/desc/reference/precision-docpage/
                    switch (obj.properties.get('metaDataProperty.GeocoderMetaData.precision')) {
                        case 'exact':
                            break;
                        case 'number':
                        case 'near':
                        case 'range':
                            error = 'Неточный адрес, требуется уточнение';
                            hint = 'Уточните номер дома';
                            break;
                        case 'street':
                            error = 'Неполный адрес, требуется уточнение';
                            hint = 'Уточните номер дома';
                            break;
                        case 'other':
                        default:
                            error = 'Неточный адрес, требуется уточнение';
                            hint = 'Уточните адрес';
                    }
                } else {
                    error = 'Адрес не найден';
                    hint = 'Уточните адрес';
                }

                // Если геокодер возвращает пустой массив или неточный результат, то показываем ошибку.
                if (error) {
                    if (ctrl_id == '#suggest1') {
                        addrFrom = null
                    } else {
                        addrTo = null
                    }
                    showError(ctrl_id, error);
                    showMessage(ctrl_id, hint);
                } else {
                    if (ctrl_id == '#suggest1') {
                        addrFrom = obj
                    } else {
                        addrTo = obj
                    }
                    showResult(ctrl_id);
                }
                if (addrFrom && addrTo) {
                    $('#notice3').css('display', 'none');
                } else {
                    $('#notice3').css('display', 'block');
                }
            }, function(e) {
                console.log(e)
            })

        }

        function showResult(ctrl_id) {
            // Удаляем сообщение об ошибке, если найденный адрес совпадает с поисковым запросом.
            $(ctrl_id).removeClass('input_error');
            $('#notice1').css('display', 'none');
            $('#notice2').css('display', 'none');
            // полный адрес для сообщения под картой.
            if (ctrl_id == '#suggest1') {
                showMessage(ctrl_id, addrFrom.getAddressLine());
            } else {
                showMessage(ctrl_id, addrTo.getAddressLine());
            }
            // Сохраняем укороченный адрес для подписи метки.
            //shortAddress = [obj.getThoroughfare(), obj.getPremiseNumber(), obj.getPremise()].join(' ');
        }

        function showError(ctrl_id, message) {
            $(ctrl_id).addClass('input_error');
            if (ctrl_id == '#suggest1') {
                $('#notice1').text(message);
                $('#notice1').css('display', 'block');
            } else {
                $('#notice2').text(message);
                $('#notice2').css('display', 'block');
            }

        }


        function showRoute(from, to) {
            // https://tech.yandex.ru/maps/jsbox/2.1/deliveryCalculator
            routePanelControl.routePanel.state.set({
                from: from,
                to: to
            });
            // Получим ссылку на маршрут.
            routePanelControl.routePanel.getRouteAsync().then(function(route) {
                // Зададим максимально допустимое число маршрутов, возвращаемых мультимаршрутизатором.
                route.model.setParams({
                    results: 1
                }, true);
                // Повесим обработчик на событие построения маршрута.
                route.model.events.add('requestsuccess', function() {
                    var activeRoute = route.getActiveRoute();
                    if (activeRoute) {
                        // Получим протяженность маршрута.
                        var length = route.getActiveRoute().properties.get("distance");
                        // Вычислим стоимость доставки.
                        price = calculate(Math.round(length.value / 1000)),
                            // Создадим макет содержимого балуна маршрута.
                            balloonContentLayout = ymaps.templateLayoutFactory.createClass(
                                '<span>Расстояние: ' + length.text + '.</span><br/>' +
                                '<span style="font-weight: bold; font-style: italic">Стоимость доставки: ' + price + ' р.</span>');
                        // Зададим этот макет для содержимого балуна.
                        route.options.set('routeBalloonContentLayout', balloonContentLayout);
                        // Откроем балун.
                        activeRoute.balloon.open();
                    }
                });
            });
        }

        function showMessage(ctrl_id, message) {
            if (ctrl_id == '#suggest1') {
                $('#messageHeader1').html('<b>Пункт отправления:</b>');
                $('#message1').html(message);
            } else {
                $('#messageHeader2').html('<b>Пункт назначения:</b>');
                $('#message2').html(message);
            }
        }

        // Функция, вычисляющая стоимость доставки.
        function calculate(routeLength) {
            return Math.max(routeLength * DELIVERY_TARIFF, MINIMUM_COST);
        }
    }

</script>
</body>

</html>
