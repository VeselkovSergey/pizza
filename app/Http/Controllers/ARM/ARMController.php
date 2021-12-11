<?php


namespace App\Http\Controllers\ARM;


use App\Helpers\ArrayHelper;

class ARMController
{

    public function AllRoutes()
    {
        $allRoutes = [
            [
                'title' => '1 Новый тип модификации (размер/толщина теста)',
                'link' => route('modification-type-create'),
                'role' => 999,
            ],
            [
                'title' => '2 Новая модификация (сама модификация со значением: Размер: 25см)',
                'link' => route('modification-create'),
                'role' => 999,
            ],
            [
                'title' => '3 Новый ингредиент (для тех. карты + продукт кока-кола состоит из ингредиента кока-кола)',
                'link' => route('ingredients-create'),
                'role' => 999,
            ],
            [
                'title' => 'Все ингредиенты (JSON)',
                'link' => route('all-ingredients'),
                'role' => 999,
            ],
            [
                'title' => '4 Поставщики (метро/рынок/рога и копыта)',
                'link' => route('supplier-create'),
                'role' => 999,
            ],
            [
                'title' => '5 Новая поставка (поставка. тут вносим ингредиенты)',
                'link' => route('supply-create'),
                'role' => 999,
            ],
            [
                'title' => '6 Новый продукт',
                'link' => route('product-create'),
                'role' => 999,
            ],
            [
                'title' => '7 Все продукты (Админка)',
                'link' => route('all-products-admin-page'),
                'role' => 999,
            ],
            [
                'title' => '8 Каталог (Сайт)',
                'link' => route('catalog'),
                'role' => 999,
            ],
            [
                'title' => '9 Все продукты (JSON)',
                'link' => route('all-products'),
                'role' => 999,
            ],
            [
                'title' => 'АРМ Администратора',
                'link' => route('administrator-arm-page'),
                'role' => 999,
            ],
            [
                'title' => 'АРМ Менеджера',
                'link' => route('manager-arm-page'),
                'role' => 777,
            ],
            [
                'title' => 'АРМ Повара',
                'link' => route('chef-arm-page'),
                'role' => 777,
            ],
            [
                'title' => 'Строю маршрут',
                'link' => 'yandexnavi://build_route_on_map?rtext=Дубна, Московская область, Россия, улица Вернова, 9  ~ улица Попова, 3, Дубна, Московская область, Россия ~ улица Понтекорво, 2, Дубна, Московская область, Россия&rtt=auto',
                'role' => 999,
            ],
        ];

        $allRoutes = ArrayHelper::ArrayToObject($allRoutes);

        return view('arm.all-routes', compact('allRoutes'));
    }
}
