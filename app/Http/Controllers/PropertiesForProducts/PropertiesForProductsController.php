<?php


namespace App\Http\Controllers\PropertiesForProducts;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\PropertiesForProducts;
use App\Models\TypesForProperties;
use Illuminate\Http\Request;

class PropertiesForProductsController extends Controller
{
    public function __construct()
    {

    }

    public function Create()
    {
        $typesProperties = TypesForProperties::all();
        return view('properties.createOrUpdate', [
            'typesProperties' => $typesProperties
        ]);
    }

    public function Save(Request $request)
    {
        $title = $request->title;
        $propertyType = $request->propertyType;
        $propertyValue = $request->propertyValue;
        if (empty($title)) {
            return ResultGenerate::Error('Пустое название');
        }
        if (empty($propertyType)) {
            return ResultGenerate::Error('Пустой тип');
        }
        if (empty($propertyValue)) {
            return ResultGenerate::Error('Пустое значение');
        }

        PropertiesForProducts::create([
            'title' => $title,
            'type_id' => $propertyType,
            'value' => $propertyValue,
        ]);
        return ResultGenerate::Success();
    }
}
