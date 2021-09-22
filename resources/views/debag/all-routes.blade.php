@extends('app')

@section('content')
    <div class="flex-column">
        <a href="{{route('product-create')}}">Новый продукт</a>
        <a href="{{route('modification-create')}}">Новая модификация</a>
        <a href="{{route('modification-type-create')}}">Новый тип модификации</a>
        <a href="{{route('ingredients-create')}}">Новый ингредиент</a>
        <a href="{{route('supplier-create')}}">Поставщики</a>
        <a href="{{route('supply-create')}}">Новая поставка</a>
    </div>
@stop

@section('js')

@stop
