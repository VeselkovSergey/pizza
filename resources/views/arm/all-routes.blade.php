@extends('app')

@section('content')
    <div class="flex-column">
        <a class="color-white" href="{{route('modification-type-create')}}">1 Новый тип модификации (размер/толщина теста)</a>
        <a class="color-white" href="{{route('modification-create')}}">2 Новая модификация (сама модификация со значением: Размер: 25см)</a>
        <a class="color-white" href="{{route('ingredients-create')}}">3 Новый ингредиент (для тех. карты + продукт кока-кола состоит из ингредиента кока-кола)</a>
        <a class="color-white" href="{{route('all-ingredients')}}">Все ингредиенты (JSON)</a>
        <a class="color-white" href="{{route('supplier-create')}}">4 Поставщики (метро/рынок/рога и копыта)</a>
        <a class="color-white" href="{{route('supply-create')}}">5 Новая поставка (поставка. тут вносим ингредиенты)</a>
        <a class="color-white" href="{{route('product-create')}}">6 Новый продукт</a>
        <a class="color-white" href="{{route('all-products-admin-page')}}">7 Все продукты (Админка)</a>
        <a class="color-white" href="{{route('catalog')}}">8 Каталог (Сайт)</a>
        <a class="color-white" href="{{route('all-products')}}">9 Все продукты (JSON)</a>
        <a class="color-white" href="{{route('administrator-arm-page')}}">АРМ Администратора</a>
        <a class="color-white" href="{{route('manager-arm-page')}}">АРМ Менеджера</a>
        <a class="color-white" href="{{route('chef-arm-page')}}">АРМ Повара</a>
        <a class="color-white" href="yandexnavi://build_route_on_map?rtext=Дубна, Московская область, Россия, улица Вернова, 9  ~ улица Попова, 3, Дубна, Московская область, Россия ~ улица Понтекорво, 2, Дубна, Московская область, Россия&rtt=auto">Строю маршрут</a>
    </div>
@stop

@section('js')

@stop
