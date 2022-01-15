@extends('app')

@section('content')

    <style>
        .width-day {
            width: {{100/7}}%
        }
        @media screen and (max-width: 960px) {
            .width-day {
                width: 100%;
            }
        }
    </style>

    <div class="mb-10 flex-wrap">
        <a class="orange-button" href="{{route('salary-page')}}">назад в зарплатный блок</a>
    </div>

    <div>

        <div>
            <label>
                Сотрудник
                <select name="">
                    <option value="">Сотрудник</option>
                </select>
            </label>
            <label>
                Начало смены
                <input type="datetime-local" value="{{now()->format('Y-m-d\TH:i')}}">
            </label>
            <label>
                Конец смены
                <input type="datetime-local" value="{{now()->format('Y-m-d\TH:i')}}">
            </label>
        </div>

        <div class="flex-wrap">
            @for($day = ((int)(now()->startOfMonth()->format('d')) - now()->startOfMonth()->dayOfWeekIso + 1); $day <= (int)(now()->endOfMonth()->format('d')); $day++)
                @php($text = $day > 0 ? $day : '')

                <div class="width-day flex-column">
                    <div class="{{$day > 0 ? 'border' : 'hide'}} m-5 flex-center-horizontal" style="flex: 1">
                        @if($day > 0)
                        <div class="w-100 flex-column">

                            <div class="text-center pos-rel">
                                {{$text}}
                                <div class="cp pos-abs top-0 right-0 p-5" onclick="OpenDetail('{{now()->startOfMonth()->format('Y-m-' . $day)}}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="w-100">
                                <div class="p-5">
                                    @if($day === 15)
                                        <div>123</div>
                                        <div>123</div>
                                        <div>123</div>
                                        <div>123</div>
                                        <div>123</div>
                                        <div>123</div>
                                        <div>123</div>
                                        <div>123</div>
                                        <div>123</div>
                                        <div>123</div>
                                        <div>123</div>
                                        <div>123</div>
                                        <div>123</div>
                                        <div>123</div>
                                    @endif
                                </div>
                            </div>

                        </div>
                        @endif
                    </div>
                </div>

            @endfor
        </div>

    </div>


@stop

@section('js')

    <script>

        function OpenDetail(date) {
            Ajax('{{route('day-detail-page')}}', 'POST', {date: date}).then((response) => {
                // ModalWindow(response);
                AddEmployeeWindow();
            });
        }

        function AddEmployeeWindow() {

            let nowTime = new Date();
            nowTime.setMinutes(nowTime.getMinutes() - nowTime.getTimezoneOffset());
            nowTime = nowTime.toISOString().slice(0, -8);

            let container = CreateElement('div', {});

            let labelEmployeeSelector = CreateElement('label', {content: 'Сотрудник', class: 'flex-column mb-10'}, container);
            let employeeSelector = CreateElement('select', {}, labelEmployeeSelector);
            CreateElement('option', {content: 'Сотрудник-1'}, employeeSelector);
            CreateElement('option', {content: 'Сотрудник-2'}, employeeSelector);

            let labelEmployeeStartOfShift = CreateElement('label', {content: 'Начало смены', class: 'flex-column mb-10'}, container);
            let fieldEmployeeStartOfShift = CreateElement('input', {attr: {type: 'datetime-local', value: nowTime},}, labelEmployeeStartOfShift);

            let labelEmployeeEndOfShift = CreateElement('label', {content: 'Конец смены', class: 'flex-column mb-10'}, container);
            let fieldEmployeeEndOfShift = CreateElement('input', {attr: {type: 'datetime-local', value: nowTime},}, labelEmployeeEndOfShift);

            let containerButton = CreateElement('div', {class: 'flex-center'}, container);
            let buttonSave = CreateElement('button', {content: 'Сохранить', class: 'orange-button'}, containerButton);

            ModalWindow(container);
        }

    </script>

@stop
