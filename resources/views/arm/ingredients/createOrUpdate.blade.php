@extends('app')

@section('content')

    <div>

        <form class="ingredients-create-or-edit-form" action="" onsubmit="return false;">

            <div>
                <label for="">Название
                    <input class="need-validate" name="title" type="text">
                    <button class="save-button orange-button">Создать</button>
                </label>
            </div>

        </form>

    </div>

@stop

@section('js')

    <script>

        let saveButton = document.body.querySelector('.save-button');
        saveButton.addEventListener('click', () => {

            let title = document.body.querySelector('input[name="title"]');
            let titleValue = title.value;
            let data = {
                title: titleValue,
            }

            if (!CheckingFieldForEmptiness('ingredients-create-or-edit-form', true)) {
                return;
            }

            Ajax("{{route('ingredients-save')}}", 'POST', data).then((response) => {
                FlashMessage(response.message);
                if (response.status === true) {
                    title.value = '';
                    title.focus();
                }
            })
        });

    </script>

@stop
