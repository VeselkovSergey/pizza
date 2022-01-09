@extends('app')

@section('content')

    <div class="mb-10">
        <a class="orange-button" href="{{route('settings-page')}}">назад в настройки</a>
    </div>

    <div>
        <label class="mb-10">Сообщение
            <textarea class="w-100" name="closedMessage">{{$closedMessageTitle}}</textarea>
        </label>
        <label class="mb-10">Начало
            <input type="datetime-local" name="start" value="{{$start}}">
        </label>
        <button class="save-button orange-button">Сохранить</button>
    </div>

@stop

@section('js')

    <script>
        let saveButton = document.body.querySelector('.save-button');
        saveButton.addEventListener('click', () => {

            let closedMessage = document.body.querySelector('textarea[name="closedMessage"]');
            let closedMessageTitle = closedMessage.value;

            let start = document.body.querySelector('input[name="start"]');
            let startValue = start.value;

            Ajax("{{route('settings-closed-message-save')}}", 'POST', {closedMessage: closedMessageTitle, start: startValue}).then((response) => {
                FlashMessage(response.message);
            })
        });
    </script>

@stop
