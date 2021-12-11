@extends('app')

@section('content')

    <style>
        body {
            background-color: whitesmoke!important;
            color: black!important;
        }
        table, th, td {
            border: 1px solid black;
        }
        .modal-window-component-container .modal-window-component .modal-window-content-container {
            background-color: white;
        }
        .modal-window-component-container .modal-window-component .modal-window-content-container .modal-window-close-button path {
            fill: black;
        }
    </style>

    <div class="mb-10">
        <a href="{{route('administrator-arm-page')}}">назад в ARM админа</a>
    </div>

    <div>
        <div>
            <table class="w-100 border">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Категория</th>
                    <th>Наиминование</th>
                    <th>Кол-во</th>
                    <th>Стоимость</th>
                    <th>Себестоимость</th>
                    <th>Наценка</th>
                </tr>
                </thead>
                <tbody>
                @foreach($productsModifications as $id => $productModification)
                <tr>
                    <td>#{{$id}}</td>
                    <td>{{$productModification->categoryTitle}}</td>
                    <td>{{$productModification->title}}</td>
                    <td>{{$productModification->amount}}</td>
                    <td>{{$productModification->price}}</td>
                    <td>{{$productModification->costPrice}}</td>
                    <td>{{number_format(((($productModification->price - $productModification->costPrice ) / $productModification->costPrice) * 100), 2)}} %</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>


@stop

@section('js')

    <script>
        function sortTable(table, col, reverse) {
            var tb = table.tBodies[0], // use `<tbody>` to ignore `<thead>` and `<tfoot>` rows
                tr = Array.prototype.slice.call(tb.rows, 0), // put rows into array
                i;
            reverse = -((+reverse) || -1);
            tr = tr.sort(function (a, b) { // sort rows
                return reverse // `-1 *` if want opposite order
                    * (a.cells[col].textContent.trim() // using `.textContent.trim()` for test
                            .localeCompare(b.cells[col].textContent.trim())
                    );
            });
            for(i = 0; i < tr.length; ++i) tb.appendChild(tr[i]); // append each row in order
        }

        function makeSortable(table) {
            var th = table.tHead, i;
            th && (th = th.rows[0]) && (th = th.cells);
            if (th) i = th.length;
            else return; // if no `<thead>` then do nothing
            while (--i >= 0) (function (i) {
                var dir = 1;
                th[i].addEventListener('click', function () {sortTable(table, i, (dir = 1 - dir))});
            }(i));
        }

        function makeAllSortable(parent) {
            parent = parent || document.body;
            var t = parent.getElementsByTagName('table'), i = t.length;
            while (--i >= 0) makeSortable(t[i]);
        }

        makeAllSortable();
    </script>

    <script>
        let orderDetailInfoButtons = document.body.querySelectorAll('.order-detail-info');
        orderDetailInfoButtons.forEach((orderDetailInfoButton) => {
            orderDetailInfoButton.addEventListener('click', (event) => {
                let orderDetailInfoContent = event.target.nextElementSibling.innerHTML;
                let modal = ModalWindow(orderDetailInfoContent);
                modal.querySelector('.order-detail-info-content').show();
            });
        });
    </script>

@stop
