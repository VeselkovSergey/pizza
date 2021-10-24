@extends('app')

@section('content')
    <div class="flex-column">
        <a href="{{route('modification-type-create')}}">1 Новый тип модификации (размер/толщина теста)</a>
        <a href="{{route('modification-create')}}">2 Новая модификация (сама модификация со значением: Размер: 25см)</a>
        <a href="{{route('ingredients-create')}}">3 Новый ингредиент (для тех. карты + продукт кока-кола состоит из ингредиента кока-кола)</a>
        <a href="{{route('all-ingredients')}}">Все ингредиенты (JSON)</a>
        <a href="{{route('supplier-create')}}">4 Поставщики (метро/рынок/рога и копыта)</a>
        <a href="{{route('supply-create')}}">5 Новая поставка (поставка. тут вносим ингредиенты)</a>
        <a href="{{route('product-create')}}">6 Новый продукт</a>
        <a href="{{route('all-products-admin-page')}}">7 Все продукты (Админка)</a>
        <a href="{{route('catalog')}}">8 Каталог (Сайт)</a>
        <a href="{{route('all-products')}}">9 Все продукты (JSON)</a>
        <a href="{{route('administrator-arm-page')}}">АРМ Администратора</a>
        <a href="{{route('manager-arm-page')}}">АРМ Менеджера</a>
        <a href="{{route('chef-arm-page')}}">АРМ Повора</a>
    </div>
@stop

@section('js')

@stop
