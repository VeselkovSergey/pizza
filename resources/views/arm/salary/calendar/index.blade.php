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
        .employee-container > div {
            font-size: 10px;
            border-bottom: 1px solid black;
        }
        .color-red {
            color: red;
        }
    </style>

    <div class="mb-10 flex-wrap">
        <a class="orange-button" href="{{route('salary-page')}}">назад в зарплатный блок</a>
    </div>

    <div>

        <div class="flex-wrap">
            @for($day = ((int)(now()->startOfMonth()->format('d')) - now()->startOfMonth()->dayOfWeekIso + 1); $day <= (int)(now()->endOfMonth()->format('d')); $day++)
                @php($text = $day > 0 ? $day : '')
                @php($date = now()->startOfMonth()->format('Y-m-' . $day))

                <div class="width-day flex-column">
                    <div class="{{$day > 0 ? 'border' : 'hide'}} m-5 flex-center-horizontal" style="flex: 1">
                        @if($day > 0)
                            <div class="w-100 flex-column">

                                <div class="text-center pos-rel">
                                    {{$text}}
                                    <div class="cp pos-abs top-0 right-0 p-5" onclick="OpenDetail('{{$date}}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                            <path
                                                d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="w-100">
                                    <div class="employee-container employee-container-{{$date}} p-5">
{{--                                        @if($day === 15)--}}
{{--                                            <div>123</div>--}}
{{--                                            <div>123</div>--}}
{{--                                            <div>123</div>--}}
{{--                                            <div>123</div>--}}
{{--                                            <div>123</div>--}}
{{--                                            <div>123</div>--}}
{{--                                            <div>123</div>--}}
{{--                                            <div>123</div>--}}
{{--                                            <div>123</div>--}}
{{--                                            <div>123</div>--}}
{{--                                            <div>123</div>--}}
{{--                                            <div>123</div>--}}
{{--                                            <div>123</div>--}}
{{--                                            <div>123</div>--}}
{{--                                        @endif--}}
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
            AddEmployeeWindow(date);
            {{--Ajax('{{route('day-detail-page')}}', 'POST', {date: date}).then((response) => {--}}
            {{--    // ModalWindow(response);--}}
            {{--    --}}
            {{--});--}}
        }

        function AddEmployeeWindow(date) {

            let nowTime = new Date();
            // nowTime.setMinutes(nowTime.getMinutes() - nowTime.getTimezoneOffset());
            // nowTime = nowTime.toISOString().slice(0, -8);
            nowTime = ("0" + nowTime.getHours()).slice(0, 2) + ':' + nowTime.getMinutes();

            let container = CreateElement('div', {});

            let labelEmployeeSelector = CreateElement('label', {
                content: 'Сотрудник',
                class: 'flex-column mb-10'
            }, container);
            let employeeSelector = CreateElement('select', {}, labelEmployeeSelector);

            @foreach($employees as $employee)
            CreateElement('option', {content: '#{{$employee->id . '# ' . $employee->name . '-' . $employee->phone}}'}, employeeSelector);
            @endforeach

            let labelEmployeeStartOfShift = CreateElement('label', {
                content: 'Начало смены',
                class: 'flex-column mb-10'
            }, container);
            let fieldEmployeeStartOfShift = CreateElement('input', {
                attr: {
                    type: 'time',
                    value: nowTime
                },
            }, labelEmployeeStartOfShift);

            let labelEmployeeEndOfShift = CreateElement('label', {
                content: 'Конец смены',
                class: 'flex-column mb-10'
            }, container);
            let fieldEmployeeEndOfShift = CreateElement('input', {
                attr: {
                    type: 'time',
                    value: nowTime
                },
            }, labelEmployeeEndOfShift);

            let containerButton = CreateElement('div', {class: 'flex-center'}, container);
            let buttonSave = CreateElement('button', {content: 'Сохранить', class: 'orange-button'}, containerButton);

            let addEmployeeModal = ModalWindow(container);

            let employeeContainer = document.body.querySelector('.employee-container-' + date);
            buttonSave.addEventListener('click', () => {
                let employee = employeeSelector.value;
                let startTime = fieldEmployeeStartOfShift.value;
                let endTime = fieldEmployeeEndOfShift.value;
                let text = employee + ' ' + startTime + ' - ' +endTime;
                let employeeInCalendarContainer = CreateElement('div', {class: 'pos-rel'}, employeeContainer);
                let employeeInCalendar = CreateElement('div', {content: text}, employeeInCalendarContainer);
                let deleteIcon = CreateElement('div', {content: 'x', class: 'pos-abs right-0 top-0 color-red cp'}, employeeInCalendarContainer);
                deleteIcon.addEventListener('click', () => {
                    alert('Удалил! ААА....');
                    employeeInCalendarContainer.remove();
                });
                addEmployeeModal.remove();
                document.body.classList.remove('scroll-off');
            });
        }

    </script>

@stop
