@extends('app')

@section('content')
    <div class="flex-column">
        <a href="{{route('modification-type-create')}}">1 Новый тип модификации</a>
        <a href="{{route('modification-create')}}">2 Новая модификация</a>
        <a href="{{route('ingredients-create')}}">3 Новый ингредиент</a>
        <a href="{{route('supplier-create')}}">4 Поставщики</a>
        <a href="{{route('supply-create')}}">5 Новая поставка</a>
        <a href="{{route('product-create')}}">6 Новый продукт</a>
    </div>
@stop

@section('js')

@stop
