@extends('app')

@section('content')

    <div class="mb-10">
        <a class="orange-button" href="{{route('supply-create-page')}}">Новая поставка</a>
    </div>

    <div>
        <div>
            <table class="w-100 border table-sort">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Поставщик</th>
                    <th>Дата поставки</th>
                    <th>Тип оплаты</th>
                    <th>Сумма</th>
                    <th>Кол-во позиций</th>
                    <th>Кто создал</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($supplies as $supply)
                    <?php /** @var \App\Models\Supply $supply */ ?>
                    <tr>
                        <td>{{$supply->id}}</td>
                        <td>{{$supply->Supplier->title}}</td>
                        <td>{{$supply->supply_date}}</td>
                        <td>{{$supply->PaymentType()}}</td>
                        <td>{{$supply->SupplySum()}}</td>
                        <td>{{$supply->Ingredients->count()}}</td>
                        <td><a href="{{route('supply-detail-page', $supply->id)}}">Подробнее</a></td>
                        <td>{{$supply->Creator->name}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@stop

@section('js')

@stop
