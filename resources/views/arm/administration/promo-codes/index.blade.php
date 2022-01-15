@extends('app')

@section('content')

    <style>
        input[name="title"] {
            text-transform: uppercase;
        }
    </style>

    <div class="mb-10 flex-wrap">
        <a class="orange-button" href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
        <a class="orange-button ml-a" href="{{route('create-promo-code-page')}}">новый промокод</a>
    </div>

    <div>
        <div class="mb-10">Промокоды</div>

        <table class="w-100 border table-sort">
            <thead>
            <tr>
                <th>ID</th>
                <th>Значение</th>
                <th>Описание</th>
                <th>Начало</th>
                <th>Конец</th>
                <th>Кол-во</th>
                <th>Использовано</th>
                <th>Активный</th>
                <th>Условие</th>
            </tr>
            </thead>
            <tbody>
            @foreach($promoCodes as $promoCode)
                <tr>
                    <td>{{$promoCode->id}}</td>
                    <td>{{$promoCode->title}}</td>
                    <td>{{$promoCode->description}}</td>
                    <td>{{$promoCode->start_date}}</td>
                    <td>{{$promoCode->end_date}}</td>
                    <td>{{$promoCode->amount}}</td>
                    <td>{{$promoCode->amount_used}}</td>
                    <td>
                        <div class="flex-center">
                            <label class="custom-checkbox-label" for="checkbox-{{$promoCode->id}}">
                                <input type="checkbox" id="checkbox-{{$promoCode->id}}" data-id="{{$promoCode->id}}" name="changeActivePromoCode" @if($promoCode->active) checked @endif />
                                <div class="custom-checkbox-slider round"></div>
                            </label>
                        </div>
                    </td>
                    <td>{{$promoCode->conditions}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>


@stop

@section('js')

    <script>

        document.body.querySelectorAll('input[name="changeActivePromoCode"]').forEach((changeActivePromoCodeField) => {
            changeActivePromoCodeField.addEventListener('change', (event) => {
                let promoCodeId = event.target.dataset.id;
                let promoCodeActive = event.target.checked;
                Ajax('{{route('change-active-promo-code')}}', 'POST', {promoCodeId: promoCodeId, promoCodeActive: promoCodeActive})
                .then((response) => {
                    FlashMessage(response.message);
                });
            });
        });

    </script>

@stop
