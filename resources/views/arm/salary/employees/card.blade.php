@extends('app')

@section('content')

    <div class="mb-10 flex-wrap">
        <a class="orange-button" href="{{route('employees-page')}}">назад к сотрудникам</a>
        <a class="orange-button ml-a" href="{{route('create-promo-code-page')}}">сохранить</a>
    </div>

    <div>
        <div class="mb-10"># {{$employee->id . ' ' . $employee->surname . ' ' . $employee->name . ' ' . $employee->patronymic . ' ' . $employee->phone}}</div>

        <div>

            <label>
                <div>Оклад</div>
                <input type="text">
            </label>

            <label>
                <div>Стоимость смены</div>
                <input type="text">
            </label>

            <label>
                <div>Стоимость часа</div>
                <input type="text">
            </label>

            <label>
                <div>Стоимость выезда</div>
                <input type="text">
            </label>

        </div>

    </div>


@stop

@section('js')

    <script>

    </script>

@stop
