@extends('app')

@section('content')
    <div class="flex-column">
        <a href="{{route('product-create')}}">Новый продукт</a>
        <a href="{{route('property-create')}}">Новое свойство</a>
        <a href="{{route('type-for-properties-create')}}">Новый тип для свойства</a>
        <a href="{{route('ingredients-create')}}">Новый ингредиент</a>
        <a href="{{route('supplier-create')}}">Поставщики</a>
        <a href="{{route('supply-create')}}">Новая поставка</a>
    </div>
@stop

@section('js')

@stop
