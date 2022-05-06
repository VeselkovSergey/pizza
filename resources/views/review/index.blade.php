@extends('app')

@section('content')

    <style>
        .review-svg {
            width: 100px;
            height: 100px;
        }
        .bad-review.selected {
            color: #ED3545;
        }
        .good-review.selected {
            color: #00fe3f;
        }
        .review-button {
            border: 1px solid grey;
            padding: 10px;
            border-radius: 10px;
            background-color: #94989e;
        }
    </style>


    <div class="">
        <h3 class="text-center">Выберите смайлик</h3>
        <div class="flex mb-10" style="justify-content: space-around;">
            <div class="review-button cp">
                <svg data-type="bad" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="review-svg bad-review bi bi-emoji-angry" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.498 3.498 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.498 4.498 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683zm6.991-8.38a.5.5 0 1 1 .448.894l-1.009.504c.176.27.285.64.285 1.049 0 .828-.448 1.5-1 1.5s-1-.672-1-1.5c0-.247.04-.48.11-.686a.502.502 0 0 1 .166-.761l2-1zm-6.552 0a.5.5 0 0 0-.448.894l1.009.504A1.94 1.94 0 0 0 5 6.5C5 7.328 5.448 8 6 8s1-.672 1-1.5c0-.247-.04-.48-.11-.686a.502.502 0 0 0-.166-.761l-2-1z"/>
                </svg>
            </div>
            <div class="review-button cp">
                <svg data-type="good" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="review-svg good-review bi bi-emoji-wink" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M4.285 9.567a.5.5 0 0 1 .683.183A3.498 3.498 0 0 0 8 11.5a3.498 3.498 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.498 4.498 0 0 1 8 12.5a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm1.757-.437a.5.5 0 0 1 .68.194.934.934 0 0 0 .813.493c.339 0 .645-.19.813-.493a.5.5 0 1 1 .874.486A1.934 1.934 0 0 1 10.25 7.75c-.73 0-1.356-.412-1.687-1.007a.5.5 0 0 1 .194-.68z"/>
                </svg>
            </div>
        </div>
        <div class="mb-5">
            <textarea name="text-review" style="min-height: 115px; height: 115px;" class="need-validate w-100 border-radius-30 p-15 not-valid" rows="5" placeholder="Текст отзыва"></textarea>
        </div>
        <div class="mb-5 @if(auth()->check()) hide @endif">
            <input name="user-name" style="margin-bottom: 4px;" type="text" class="need-validate w-100 border-radius-30 p-15 not-valid" placeholder="Ваше имя" @if(auth()->check()) value="{{auth()->user()->name}}" @endif>
        </div>
        <div class="mb-5 @if(auth()->check()) hide @endif">
            <input name="user-phone" style="margin-bottom: 4px;" type="text" class="phone-mask need-validate w-100 border-radius-30 p-15 not-valid" placeholder="Номер телефона" @if(auth()->check()) value="{{auth()->user()->phone}}" @endif>
        </div>
        <div class="w-100">
            <button class="review-send-button w-100 orange-button">Отправить</button>
        </div>
    </div>

@stop

@section('js')

    <script>
        startTrackingNumberInput();
        document.body.querySelectorAll('.review-button').forEach((button) => {
            button.addEventListener('click', () => {
                document.body.querySelectorAll('.review-svg').forEach((svg) => {
                    svg.classList.remove('selected');
                });
                button.querySelector('.review-svg').classList.add('selected');
            });
        });

        document.body.querySelector('.review-send-button').addEventListener('click', () => {
            const reviewText = document.body.querySelector('[name="text-review"]');
            const userName = document.body.querySelector('[name="user-name"]');
            const userPhone = document.body.querySelector('[name="user-phone"]');

            if (!document.body.querySelector('.review-svg.selected')) {
                return ModalWindowFlash('Выберите пожалуйста смайлик');
            }

            if (userName.value.length === 0) {
                return ModalWindowFlash('Заполните пожалуйста Имя');
            }

            if (userPhone.value.length === 0) {
                return ModalWindowFlash('Заполните пожалуйста Номер телефона');
            }

            if (reviewText.value.length === 0) {
                return ModalWindowFlash('Напишите пожалуйста чуть чуть текста');
            }

            LoaderShow();

            Ajax("{{route('review-create')}}", "POST", {
                name: userName.value,
                phone: userPhone.value,
                text: reviewText.value,
                type: document.body.querySelector('.review-svg.selected').dataset.type,
                userId: {{auth()->check() ? auth()->user()->id : -1}},
            }).finally(() => {
                LoaderHide();
                ModalWindow('Спасибо за отзыв!');
                setTimeout(() => {
                    // location.href = '/';
                }, 2000);
            })

        });
    </script>

@stop
