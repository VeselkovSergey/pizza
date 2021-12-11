@extends('app')

@section('content')
    <div class="flex-column">
        @foreach($allRoutes as $route)
            @if(auth()->user()->role_id >= $route->role)
                <a class="color-white" href="{{$route->link}}">{{$route->title}}</a>
            @endif
        @endforeach
        <a class="color-white" href=""></a>
    </div>
@stop

@section('js')

@stop
