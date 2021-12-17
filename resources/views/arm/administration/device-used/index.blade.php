@extends('app')

@section('content')

    <style>
        body {
            background-color: whitesmoke!important;
            color: black!important;
        }
        table, th, td {
            border: 1px solid black;
        }
    </style>

    <div class="mb-10">
        <a href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div class="mb-10">
        <div>iPhone: {{$typeDevice['iphone']}}</div>
        <div>Android: {{$typeDevice['android']}}</div>
        <div>PC: {{$typeDevice['pc']}}</div>
    </div>

    <div>
        <div>
            <table class="w-100 border table-sort">
                <thead>
                <tr>
                    <th>Браузер</th>
                    <th>Ширина экрана</th>
                    <th>Высота экрана</th>
                    <th>Тип устройства</th>
                </tr>
                </thead>
                <tbody>
                @foreach($devicesInfo as $deviceInfo)
                    <tr>
                        <td>{{$deviceInfo->userAgent}}</td>
                        <td>{{$deviceInfo->screenWidth}}</td>
                        <td>{{$deviceInfo->screenHeight}}</td>
                        <td>{{$deviceInfo->typeDeviceName}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@stop

@section('js')

@stop
