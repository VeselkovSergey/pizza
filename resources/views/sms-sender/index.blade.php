@extends('app')

@section('content')

    <div>
        <a href="{{route('administrator-arm-page')}}" class="orange-button">назад</a>
    </div>

    <div class="sms-container">
        <label>
            <div>Номер телефона</div>
            <input class="need-validate w-100 phone-mask" type="text" name="phone" placeholder="89151640548">
        </label>
        <label>
            <div>Текст сообщения</div>
            <textarea class="need-validate w-100" name="text" rows="5"></textarea>
        </label>
        <button class="send-button orange-button">Отправить</button>
    </div>

@stop

@section('js')

    <script>
        startTrackingNumberInput();
        document.body.querySelector('.send-button').addEventListener('click', () => {
            if (!CheckingFieldForEmptiness('sms-container', true)) {
                return;
            }
            LoaderShow();
            Ajax("{{route('send-sms')}}", "POST", GetDataFormContainer('sms-container')).then((response) => {
                FlashMessage(response);
            }).finally(() => LoaderHide());
        });
    </script>

@stop
