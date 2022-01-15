@extends('app')

@section('content')

    <div class="mb-10 flex-wrap">
        <a class="orange-button" href="{{route('salary-page')}}">назад в зарплатный блок</a>
    </div>

    <div>
        <div class="mb-10">Сотрудники</div>

        <table class="w-100 border table-sort">
            <thead>
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Номер</th>
                <th>Роль</th>
                <th>ID чата в телеграм</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($usersEmployees as $user)
                <?php /** @var \App\Models\User $user */?>
                <tr class="user-info-container" data-user-id="{{$user->id}}">
                    <td>{{$user->id}}</td>
                    <td><input name="name" class="edit-field" readonly type="text" value="{{$user->name}}"></td>
                    <td>{{$user->phone}}</td>
                    <td><input name="role_id" class="edit-field" readonly type="text" value="{{$user->role_id}}"></td>
                    <td><input name="telegram_chat_id" class="edit-field" readonly type="text" value="{{$user->telegram_chat_id}}"></td>
                    <td class="text-center"><a href="{{route('employee-card-page', $user->id)}}">карточка сотрудника</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>


@stop

@section('js')

    <script>

    </script>

@stop
