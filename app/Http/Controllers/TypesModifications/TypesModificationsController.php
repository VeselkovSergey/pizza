<?php


namespace App\Http\Controllers\TypesModifications;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\Modifications;
use App\Models\TypesModifications;
use Illuminate\Http\Request;

class TypesModificationsController extends Controller
{
    public function Index()
    {
        $typesModifications = TypesModifications::all();
        return view('arm.types-modifications.index', compact('typesModifications'));
    }
    public function Create()
    {
        return view('arm.types-modifications.createOrUpdate');
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

        TypesModifications::create([
            'title' => $title,
            'value_unit' => $valueUnit,
        ]);
        return ResultGenerate::Success();
    }
}
