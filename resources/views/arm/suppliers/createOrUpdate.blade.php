@extends('app')

@section('content')

    <div>

        <div class="mb-10">
            <form class="supplier-create-or-edit-form" action="" onsubmit="return false;">
                <label>Название поставщика
                    <input class="need-validate" name="title" type="text">
                    <button class="save-button orange-button">Создать</button>
                </label>
            </form>
        </div>

        <div>
            <table class="w-100 border table-sort">
                <thead>
                <tr>
                    <th class="text-center w-0">#</th>
                    <th>Наименование</th>
                </tr>
                </thead>
                <tbody>
                @foreach($suppliers as $supplier)
                    <tr>
                        <td>{{$supplier->id}}</td>
                        <td>{{$supplier->title}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

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

            if (!CheckingFieldForEmptiness('supplier-create-or-edit-form', true)) {
                return;
            }

            Ajax("{{route('supplier-save')}}", 'POST', data).then((response) => {
                FlashMessage(response.message);
                if (response.status === true) {
                    location.reload();
                }
            })
        });

    </script>

@stop
