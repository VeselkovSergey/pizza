@extends('app')

@section('content')

    <style>
        .edit-field {
            cursor: pointer;
            width: -webkit-fill-available;
        }
        .edit-field:not(:read-only) {
            transform: scale(1.2);
        }
    </style>

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
                    <th>ID чата в телеграм</th>
                    <th>Кол-во заказов</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <?php /** @var \App\Models\User $user */?>
                    <tr class="user-info-container" data-user-id="{{$user->id}}">
                        <td>{{$user->id}}</td>
                        <td><input name="name" class="edit-field" readonly type="text" value="{{$user->name}}"></td>
                        <td>{{$user->phone}}</td>
                        <td><input name="role_id" class="edit-field" readonly type="text" value="{{$user->role_id}}"></td>
                        <td><input name="telegram_chat_id" class="edit-field" readonly type="text" value="{{$user->telegram_chat_id}}"></td>
                        <td><a href="{{route('administrator-arm-user-orders-page', $user->id)}}">{{$user->Orders->count()}}</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@stop

@section('js')

    <script>

        document.body.querySelectorAll('.edit-field').forEach((field) => {
            field.addEventListener('dblclick', (event) => {
                event.target.removeAttribute('readonly');
            });

            field.addEventListener('blur', (event) => {
                event.target.setAttribute('readonly', 'readonly');
                let productContainer = event.target.closest('.user-info-container');
                let userId = productContainer.dataset.userId;
                let value = {};
                value[event.target.name] = event.target.value;
                SaveChanges (userId, value);
            });
        });

        function SaveChanges (userId, data) {
            Ajax("{{route('administrator-arm-user-save-changes')}}", "POST", {userId: userId, data: JSON.stringify(data)}).then((response) => {
                FlashMessage(response.message);
            });
        }

    </script>

@stop
