@extends('app')

@section('content')

    <div>
        <div>Настройки</div>
        <div>
            <a href="{{route('settings-closed-message-page')}}">Редактирование текста закрытия</a>
        </div>
    </div>

@stop

@section('js')

@stop
