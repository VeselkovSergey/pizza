@extends('app')

@section('content')

    <div class="mb-10">
        <a class="orange-button" href="{{route('administrator-arm-users-page')}}">назад к пользователям</a>
        <a class="orange-button" href="{{route('logout-all-devices')}}">удалить все сессии</a>
    </div>

    <div>
        <div>Сессии</div>
        <div>
            @foreach($sessionsArr as $session)
                <div>
                    <pre>{{print_r($session)}}</pre>
                </div>
            @endforeach
        </div>
    </div>


@stop

@section('js')

@stop
