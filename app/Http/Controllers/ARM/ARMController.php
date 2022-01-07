<?php


namespace App\Http\Controllers\ARM;


use App\Helpers\ArrayHelper;

class ARMController
{

    public static function AllRoutes()
    {
        $allRoutes = [
            [
                'title' => 'Новый тип модификации (размер/толщина теста)',
                'link' => route('modification-type-create-page'),
                'role' => [999],
            ],
            [
                'title' => 'Новая модификация (сама модификация со значением: Размер: 25см)',
                'link' => route('modification-create-page'),
                'role' => [999],
            ],
            [
                'title' => 'Новый ингредиент (для тех. карты + продукт кока-кола состоит из ингредиента кока-кола)',
                'link' => route('ingredients-create-page'),
                'role' => [999],
            ],
            [
                'title' => 'Все ингредиенты (JSON)',
                'link' => route('all-ingredients'),
                'role' => [999],
            ],
            [
                'title' => 'Поставщики',
                'link' => route('supplier-create-page'),
                'role' => [999, 777],
            ],
            [
                'title' => 'Поставки',
                'link' => route('supplies-page'),
                'role' => [999, 777],
            ],
            [
                'title' => 'Новый продукт',
                'link' => route('product-create-page'),
                'role' => [999],
            ],
            [
                'title' => 'Все продукты (Админка)',
                'link' => route('all-products-admin-page'),
                'role' => [999],
            ],
            [
                'title' => 'Каталог (Сайт)',
                'link' => route('catalog'),
                'role' => [999],
            ],
            [
                'title' => 'Все продукты (JSON)',
                'link' => route('all-products'),
                'role' => [999],
            ],
            [
                'title' => 'АРМ Администратора',
                'link' => route('administrator-arm-page'),
                'role' => [999],
            ],
            [
                'title' => 'АРМ Менеджера',
                'link' => route('manager-arm-page'),
                'role' => [999, 777],
            ],
            [
                'title' => 'АРМ Повара',
                'link' => route('chef-arm-page'),
                'role' => [999, 777],
            ],
            [
                'title' => 'Строю маршрут',
                'link' => 'yandexnavi://build_route_on_map?rtext=Дубна, Московская область, Россия, улица Вернова, 9  ~ улица Попова, 3, Дубна, Московская область, Россия ~ улица Понтекорво, 2, Дубна, Московская область, Россия&rtt=auto',
                'role' => [999],
            ],
        ];

        return ArrayHelper::ArrayToObject($allRoutes);
    }

    public function AllRoutesPage()
    {
        $allRoutes = self::AllRoutes();

        return view('arm.all-routes', compact('allRoutes'));
    }
}
