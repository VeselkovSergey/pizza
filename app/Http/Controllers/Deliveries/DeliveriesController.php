<?php


namespace App\Http\Controllers\Deliveries;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Deliveries;
use App\Models\Ingredients;
use App\Models\ProductProperties;
use App\Models\Products;
use App\Models\PropertiesForProducts;
use Illuminate\Http\Request;

class DeliveriesController extends Controller
{
    public function __construct()
    {

    }

    public function Create()
    {
        return view('deliveries.createOrUpdate');
    }

    public function Save(Request $request)
    {
        $title = $request->title;
        if (empty($title)) {
            return ResultGenerate::Error('Пустое название');
        }

        Deliveries::create([
            'title' => $title
        ]);
        return ResultGenerate::Success();
    }
}
