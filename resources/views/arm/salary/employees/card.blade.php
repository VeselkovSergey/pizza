@extends('app')

@section('content')

    <div class="mb-10 flex-wrap">
        <a class="orange-button" href="{{route('employees-page')}}">назад к сотрудникам</a>
        <a class="orange-button ml-a" href="{{route('create-promo-code-page')}}">сохранить</a>
    </div>

    <div>
        <div class="mb-10"># {{$employee->id . ' ' . $employee->name . ' ' . $employee->phone}}</div>

        <div>

            <div class="flex-center-vertical">
                <span class="mr-5">Работает</span>
                <label class="custom-checkbox-label" for="isEmployee">
                    <input type="checkbox" id="isEmployee" name="isEmployee" @if($employee->is_employee) checked @endif/>
                    <div class="custom-checkbox-slider round"></div>
                </label>
            </div>


            <div class="flex-wrap">
                <label class="w-fit mr-5">
                    <div>Фамилия</div>
                    <input type="text" value="{{$employee->surname}}">
                </label>

                <label class="w-fit mr-5">
                    <div>Имя</div>
                    <input type="text" value="{{$employee->name}}">
                </label>

                <label class="w-fit mr">
                    <div>Отчество</div>
                    <input type="text" value="{{$employee->patronymic}}">
                </label>
            </div>

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

{{--        <table class="w-100 border table-sort">--}}
{{--            <thead>--}}
{{--            <tr>--}}
{{--                <th>ID</th>--}}
{{--                <th>Имя</th>--}}
{{--                <th>Номер</th>--}}
{{--                <th>Роль</th>--}}
{{--                <th>ID чата в телеграм</th>--}}
{{--                <th></th>--}}
{{--            </tr>--}}
{{--            </thead>--}}
{{--            <tbody>--}}
{{--            @foreach($usersEmployees as $user)--}}
{{--                <?php /** @var \App\Models\User $user */?>--}}
{{--                <tr class="user-info-container" data-user-id="{{$user->id}}">--}}
{{--                    <td>{{$user->id}}</td>--}}
{{--                    <td><input name="name" class="edit-field" readonly type="text" value="{{$user->name}}"></td>--}}
{{--                    <td>{{$user->phone}}</td>--}}
{{--                    <td><input name="role_id" class="edit-field" readonly type="text" value="{{$user->role_id}}"></td>--}}
{{--                    <td><input name="telegram_chat_id" class="edit-field" readonly type="text" value="{{$user->telegram_chat_id}}"></td>--}}
{{--                    <td class="text-center"><a href="{{route('employee-card-page', $user->id)}}">карточка сотрудника</a></td>--}}
{{--                </tr>--}}
{{--            @endforeach--}}
{{--            </tbody>--}}
{{--        </table>--}}

    </div>


@stop

@section('js')

    <script>

    </script>

@stop
