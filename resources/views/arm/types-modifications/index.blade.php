@extends('app')

@section('content')

    <div class="mb-10">
        <a class="orange-button" href="{{route('type-modification-create-page')}}">новый тип модификации</a>
    </div>

    <div>
        <div>Типы модификаций</div>
        <div>
            <table class="w-100 border table-sort">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Значение</th>
                </tr>
                </thead>
                <tbody>
                @foreach($typesModifications as $typeModification)
                    <?php /** @var \App\Models\TypesModifications $typeModification */?>
                    <tr class="container" data-id="{{$typeModification->id}}">
                        <td>{{$typeModification->id}}</td>
                        <td>{{$typeModification->title}}</td>
                        <td>{{$typeModification->value_unit}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@stop

@section('js')

@stop
