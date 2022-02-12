@extends('app')

@section('content')

    <style>
        input[name="title"] {
            text-transform: uppercase;
        }
    </style>

    <div class="mb-10">
        <a class="orange-button" href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div>
        <div class="mb-10">Создание промокода</div>

        <div class="promo-code-info">
            <label class="mb-10">
                <input type="text" id="title" class="need-validate" value="{{strtoupper(\Nette\Utils\Random::generate())}}">
                Значение
            </label>
            <label class="mb-10">
                <textarea id="description" class="need-validate">Супер промокод</textarea>
                Описание
            </label>
            <label class="mb-10">
                <input type="datetime-local" id="start_date" value="{{now()->format('Y-m-d\TH:m')}}" class="need-validate">
                Дата начала
            </label>
            <label class="mb-10">
                <input type="datetime-local" id="end_date" value="{{now()->addYear()->format('Y-m-d\TH:m')}}" class="need-validate">
                Дата окончания
            </label>
            <label class="mb-10">
                <input type="text" id="amount" value="1" class="need-validate">
                Общее количество (> или равно кол-ву на пользователя)
            </label>
            <label class="mb-10">
                <input type="text" id="user_limit" value="1" class="need-validate">
                Количество на пользователя (0 = безлимит)
            </label>

            <div>
                <div>Скидка на весь заказ</div>
                <label class="mb-10">
                    <input type="text" id="generalDiscountPercent" value="0" readonly class="need-validate">
                    Общая скидка на заказ в процентах
                </label>
                <label class="mb-10">
                    <input type="text" id="generalDiscountSum" value="0" readonly class="need-validate">
                    Общая скидка на заказ в рублях
                </label>
            </div>

            <div>
                <div>Скидка на конкретную(-ые) модификацию(-и)</div>
                <label class="mb-10">
                    <input type="text" id="everyDiscountPercent" value="0" class="need-validate">
                    Скидка на модификацию продукта в процентах
                </label>
                <label class="mb-10">
                    <input type="text" id="everyDiscountSum" value="0" class="need-validate">
                    Скидка на модификацию продукта в деньгах
                </label>
                <label class="mb-10">
                    <input type="text" id="everySalePrice" value="0" class="need-validate">
                    Стоимость модификации продукта в деньгах
                </label>
                <label class="mb-10">
                    <input type="text" id="everyReiterationsCounts" value="0" class="need-validate">
                    На какое количество модификаций применится
                </label>
            </div>

            <div>
                <div>Модификаторы участники</div>
                <div>

                    <div class="flex mb-15">
                        <label class="custom-checkbox-label" for="all-modifications">
                            <input type="checkbox" id="all-modifications"/>
                            <div class="custom-checkbox-slider round"></div>
                        </label>
                        <span>Все</span>
                    </div>

                    @foreach($products as $product)
                        <?php /** @var \App\Models\Products $product */ ?>
                        @foreach($product->Modifications as $productModification)
                                <?php /** @var \App\Models\ProductModifications $productModification */ ?>

                                    <div class="flex mb-5">
                                        <label class="custom-checkbox-label" for="modification-{{$productModification->id}}">
                                            <input class="modification-checkbox" type="checkbox" id="modification-{{$productModification->id}}" name="modifications[{{$productModification->id}}]"/>
                                            <div class="custom-checkbox-slider round"></div>
                                        </label>
                                        <span>{{$product->title . ' ' . $productModification->Modification->title . ' ' . $productModification->Modification->value}}</span>
                                    </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

        </div>

        <button class="create-promo-code orange-button">Создать промокод</button>

    </div>


@stop

@section('js')

    <script>

        let createPromoCodeButton = document.body.querySelector('.create-promo-code');
        if (createPromoCodeButton) {
            createPromoCodeButton.addEventListener('click', () => {
                if (CheckingFieldForEmptiness('promo-code-info', true)) {
                    let data = GetDataFormContainer('promo-code-info');
                    console.log(data);
                    CreatePromoCode(data)
                }

            });
        }

        function CreatePromoCode(data) {
            Ajax("{{route('create-promo-code')}}", "POST", data).then((response) => {
                if (response.status === true) {
                    ModalWindow(response.message, () => {
                        location.href = "{{route('all-promo-codes-page')}}";
                    });
                } else {
                    ModalWindow(response.message);
                }
            });
        }

        document.getElementById('all-modifications').addEventListener('change', (event) => {
            const checked = event.target.checked;
            document.body.querySelectorAll('.modification-checkbox').forEach((input) => {
                input.checked = checked;
            });
        });

    </script>

@stop
