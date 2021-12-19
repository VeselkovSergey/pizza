@extends('app')

@section('content')

    <div class="mb-10">
        <a href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div>
        <div>Пользователи</div>
        <div>
            <table class="w-100 border table-sort">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Номер</th>
                    <th>Роль</th>
                    <th>Кол-во заказов</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <?php /** @var \App\Models\User $user */?>
                    <tr>
                        <td>{{$user->id}}</td>
                        <td>{{$user->name}}</td>
                        <td>{{$user->phone}}</td>
                        <td>{{$user->role_id}}</td>
                        <td><a href="{{route('administrator-arm-user-orders-page', $user->id)}}">{{$user->Orders->count()}}</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@stop

@section('js')

@stop
