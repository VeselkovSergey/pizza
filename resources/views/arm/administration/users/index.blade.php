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
        <a class="orange-button" href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div>
        <div>Пользователи</div>
        <div>
            <table class="w-100 border table-sort">
                <thead>
                <tr>
                    <th class="w-0">ID</th>
                    <th>Имя</th>
                    <th class="w-0">Номер</th>
                    <th class="w-0">Роль</th>
                    <th class="w-0">Работает</th>
                    <th class="w-0">ID чата в телеграм</th>
                    <th class="w-0">Кол-во заказов</th>
                    <th class="w-0"></th>
                    <th class="w-0"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <?php /** @var \App\Models\User $user */?>
                    <tr class="user-info-container hover-color" data-user-id="{{$user->id}}">
                        <td class="text-center">{{$user->id}}</td>
                        <td><input name="name" class="edit-field" readonly type="text" value="{{$user->name}}"></td>
                        <td class="text-center">{{$user->phone}}</td>
                        <td class="text-center"><input name="role_id" class="edit-field" readonly type="text" value="{{$user->role_id}}"></td>
                        <td class="text-center">
                            <div class="flex-center">
                                <label class="custom-checkbox-label" for="is_employee-{{$user->id}}">
                                    <input class="edit-field" type="checkbox" id="is_employee-{{$user->id}}" name="is_employee" @if($user->is_employee) checked @endif/>
                                    <div class="custom-checkbox-slider round"></div>
                                </label>
                            </div>
                        </td>
                        <td class="text-center"><input name="telegram_chat_id" class="edit-field" readonly type="text" value="{{$user->telegram_chat_id}}"></td>
                        <td class="text-center">{{$user->Orders->count()}}</td>
                        <td class="text-center"><a href="{{route('administrator-arm-user-orders-page', $user->id)}}">к заказам</a></td>
                        <td class="text-center"><a href="{{route('all-sessions-page')}}">к сессиям</a></td>

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

            field.addEventListener('change', (event) => {
                event.target.type !== 'checkbox' ? event.target.setAttribute('readonly', 'readonly') : '';
                let productContainer = event.target.closest('.user-info-container');
                let userId = productContainer.dataset.userId;
                let value = {};
                value[event.target.name] = event.target.type === 'checkbox' ? (event.target.checked ? 1 : 0) : event.target.value;
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
