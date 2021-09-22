<?php


namespace App\Http\Controllers\Supply;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Deliveries;
use App\Models\Ingredients;
use App\Models\ProductProperties;
use App\Models\Products;
use App\Models\PropertiesForProducts;
use App\Models\Supply;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    public function __construct()
    {

    }

    public function Create()
    {
        return view('Supply.createOrUpdate');
    }

    public function Save(Request $request)
    {
        $title = $request->title;
        if (empty($title)) {
            return ResultGenerate::Error('Пустое название');
        }

        Supply::create([
            'title' => $title
        ]);
        return ResultGenerate::Success();
    }
}
