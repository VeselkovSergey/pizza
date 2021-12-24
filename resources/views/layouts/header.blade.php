<div class="logo-container flex-center">
    <a class="flex-center w-100" href="{{route('home-page')}}">
        <div class="logo w-100 h-100">
            <img class="w-100" src="{{url('logo.png')}}" alt="">
        </div>
    </a>
</div>

<a class="flex-center mx-a clear-a color-white @if(auth()->check() && auth()->user()->IsManager()) hide @endif" href="{{route('home-page')}}">
    <div style="font-style: italic;font-weight: bold;font-size: large;">
        BROпицца
    </div>
</a>

<div class="button-menu flex-center cp mr-10 @if(auth()->check() && auth()->user()->IsManager()) ml-a @endif">
    <div class="color-white flex-center border-radius-5 px-10 border-orange">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
        </svg>
    </div>
</div>

<div class="flex-column-center">

    <div class="container-profile-and-basket flex-center pr-5">
        <div onclick="{{$actionConditionAuth}}" class="container-profile flex-column-center text-center cp p-5 {{$authCheck ? 'color-green' : ''}}">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                </svg>
            </div>
            <div class="text-center">
                @if($authCheck)
                    Профиль
                @else
                    Вход
                @endif
            </div>
        </div>
        <div class="button-basket color-white flex-column-center text-center cp">
            <div class="pos-rel">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-basket" viewBox="0 0 16 16">
                    <path d="M5.757 1.071a.5.5 0 0 1 .172.686L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1v4.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 13.5V9a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h1.217L5.07 1.243a.5.5 0 0 1 .686-.172zM2 9v4.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V9H2zM1 7v1h14V7H1zm3 3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 4 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 6 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 8 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5z"/>
                </svg>
                <div class="amount-item-in-basket hide  pos-abs right-0 p-5 bg-orange color-white">
                    0
                </div>
            </div>
            <div class="button-text text-center">
                Корзина
            </div>
        </div>
    </div>

    <div class="phone-container-header flex-center">
        <a class="text-center color-white" style="text-decoration: none;" href="tel:+7(926)585-36-21">+7(926)585-36-21</a>
    </div>

</div>

        @if(auth()->check() && auth()->user()->IsManager())
            <a href="{{route('manager-arm-orders-page')}}" class="clear-a color-white alarm-container flex-column-center text-center cp p-5">
                <div class="pos-rel">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-alarm" viewBox="0 0 16 16">
                        <path d="M8.5 5.5a.5.5 0 0 0-1 0v3.362l-1.429 2.38a.5.5 0 1 0 .858.515l1.5-2.5A.5.5 0 0 0 8.5 9V5.5z"/>
                        <path d="M6.5 0a.5.5 0 0 0 0 1H7v1.07a7.001 7.001 0 0 0-3.273 12.474l-.602.602a.5.5 0 0 0 .707.708l.746-.746A6.97 6.97 0 0 0 8 16a6.97 6.97 0 0 0 3.422-.892l.746.746a.5.5 0 0 0 .707-.708l-.601-.602A7.001 7.001 0 0 0 9 2.07V1h.5a.5.5 0 0 0 0-1h-3zm1.038 3.018a6.093 6.093 0 0 1 .924 0 6 6 0 1 1-.924 0zM0 3.5c0 .753.333 1.429.86 1.887A8.035 8.035 0 0 1 4.387 1.86 2.5 2.5 0 0 0 0 3.5zM13.5 1c-.753 0-1.429.333-1.887.86a8.035 8.035 0 0 1 3.527 3.527A2.5 2.5 0 0 0 13.5 1z"/>
                    </svg>
                </div>
            </a>
            <a href="{{route('chef-arm-orders-page')}}" class="clear-a color-white flex-column-center text-center cp p-5">
                <div class="pos-rel">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 1000 1000" width="32" height="32" style="fill: white;">
                        <g>
                            <g transform="translate(0.000000,511.000000) scale(0.100000,-0.100000)">
                                <path d="M4841.1,3452.8c-183.8-67-300.5-237.3-300.5-440.2c0-147.4,47.9-250.7,151.2-340.7c78.5-67,78.5-67,78.5-202.9v-134l-147.4-13.4c-597.2-51.7-1129.3-197.1-1661.4-459.4c-467-227.8-882.4-522.5-1246.1-886.2C894.3,160.6,415.8-899.8,337.3-2065.5L324-2262.6h-82.3c-109.1,0-141.6-15.3-141.6-65.1c0-49.8,371.3-581.9,493.8-706.3c112.9-114.8,243.1-179.9,415.4-204.8c183.8-24.9,7797.9-24.9,7979.7,0c170.3,24.9,273.7,72.7,384.7,174.2C9480.8-2967,9900-2375.5,9900-2322c0,44-36.4,59.4-141.7,59.4h-82.3l-13.4,197.1c-42.1,637.4-197.1,1221.2-472.8,1774.3C8464.5,1169.3,7078.7,2139.7,5474.7,2312c-103.3,9.6-199.1,19.1-216.3,19.1c-23,0-28.7,26.8-28.7,143.6v143.6l84.2,78.5c47.9,42.1,95.7,109.1,109.1,147.4c32.5,95.7,28.7,266.1-5.7,348.4C5321.6,3416.4,5065.1,3531.3,4841.1,3452.8z"/>
                            </g>
                        </g>
                    </svg>
                </div>
            </a>
        @endif
