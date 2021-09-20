<?php


namespace App\Http\Controllers\TypeForProperties;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\PropertiesForProducts;
use App\Models\TypesForProperties;
use Illuminate\Http\Request;

class TypeForPropertiesController extends Controller
{
    public function __construct()
    {

    }

    public function Create()
    {
        return view('typeForProperties.createOrUpdate');
    }

    public function Save(Request $request)
    {
        $title = $request->title;
        $valueUnit = $request->valueUnit;
        if (empty($title)) {
            return ResultGenerate::Error('Пустое название');
        }
        if (empty($valueUnit)) {
            return ResultGenerate::Error('Пустое значение');
        }

        TypesForProperties::create([
            'title' => $title,
            'value_unit' => $valueUnit,
        ]);
        return ResultGenerate::Success();
    }
}
