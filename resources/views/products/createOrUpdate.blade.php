@extends('app')

@section('content')

    <div>

        <form class="product-create-or-edit-form" action="" onsubmit="return false;">

            <div>
                <label for="">Название</label>
                <input class="need-validate" name="title" type="text">
            </div>

            <div>Свойства</div>
            @foreach($propertiesForProducts as $propertyForProducts)
                <div class="border m-5 p-5 w-fit">
                    <label for="">{{$propertyForProducts->title . ' - ' . $propertyForProducts->value . ' ' . $propertyForProducts->Type->value_unit}}</label>
                    <input type="checkbox">
                    <div class="border m-5 p-5 w-fit">
                        @foreach($ingredients as $ingredient)
                            <div class="border m-5 p-5 flex w-fit">
                                <div class="border p-5">
                                    <label for="">{{$ingredient->title}}</label>
                                    <input type="checkbox">
                                </div>
                                <div class="border p-5">
                                    <label for="">Количество</label>
                                    <input type="text">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div>
                <label for="">Себестоимость</label>
                <input type="text">
            </div>

            <div>
                <label for="">Наценка</label>
                <input type="text">
            </div>

            <div>
                <label for="">Продажная стоимость</label>
                <input type="text">
            </div>

            <div>
                <button class="save-button">Создать</button>
            </div>

        </form>

    </div>

@stop

@section('js')

    <script>

        let saveButton = document.body.querySelector('.save-button');
        saveButton.addEventListener('click', () => {

            let title = document.body.querySelector('input[name="title"]').value;

            if (!CheckingFieldForEmptiness('product-create-or-edit-form', true)) {
                return;
            }

            Ajax("{{route('product-save')}}", 'POST', {title: title}).then((response) => {
                FlashMessage(response.message);
            })
        });

    </script>

@stop
