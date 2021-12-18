@extends('app')

@section('content')
    <div class="flex-column">
        @foreach($allRoutes as $route)
            @if(auth()->user()->role_id >= $route->role)
                <a href="{{$route->link}}">{{$route->title}}</a>
            @endif
        @endforeach
        <a href="{{route('home-page')}}?force-update=true">Обновление системного кэша</a>
    </div>
@stop

@section('js')

@stop
