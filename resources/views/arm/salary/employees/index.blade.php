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
                <th class="w-0">ID</th>
                <th>ФИО</th>
                <th class="w-0">Номер</th>
                <th class="w-0">Телеграмм</th>
                <th class="w-0"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($usersEmployees as $user)
                <?php /** @var \App\Models\User $user */?>
                <tr class="user-info-container" data-user-id="{{$user->id}}">
                    <td class="text-center">{{$user->id}}</td>
                    <td class="text-center">{{$user->surname . ' ' . $user->name . ' ' . $user->patronymic}}</td>
                    <td class="text-center">{{$user->phone}}</td>
                    <td class="text-center">{{$user->telegram_chat_id}}</td>
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
