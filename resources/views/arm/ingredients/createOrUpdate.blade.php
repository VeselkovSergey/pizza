@extends('app')

@section('content')

    <div>

        <div class="ingredients-create-or-edit">

            <div>
                <label class="mb-10">
                    <input class="need-validate" name="title" type="text">
                    Название
                </label>
                <label class="mb-10">
                    <input class="need-validate" name="description" type="text">
                    Описание (где используется)
                </label>
                <button class="save-button orange-button">Создать</button>
            </div>

        </div>

    </div>

@stop

@section('js')

    <script>

        let saveButton = document.body.querySelector('.save-button');
        saveButton.addEventListener('click', () => {


            if (!CheckingFieldForEmptiness('ingredients-create-or-edit', true)) {
                return;
            }

            LoaderShow();

            const data = GetDataFormContainer('ingredients-create-or-edit');

            Ajax("{{route('ingredients-save')}}", 'POST', data).then((response) => {
                LoaderHide();
                FlashMessage(response.message);
                if (response.status === true) {
                    location.reload();
                }
            })
        });

    </script>

@stop
