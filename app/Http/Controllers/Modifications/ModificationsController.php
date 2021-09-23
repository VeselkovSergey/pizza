<?php


namespace App\Http\Controllers\Modifications;

use App\Helpers\ResultGenerate;
use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\Modifications;
use App\Models\TypesModifications;
use Illuminate\Http\Request;

class ModificationsController extends Controller
{
    public function __construct()
    {

    }

    public function Create()
    {
        $typeModifications = TypesModifications::all();
        return view('modifications.createOrUpdate', [
            'typeModifications' => $typeModifications
        ]);
    }

    public function Save(Request $request)
    {
        $title = $request->title;
        $modificationType = $request->modificationType;
        $modificationValue = $request->modificationValue;
        if (empty($title)) {
            return ResultGenerate::Error('Пустое название');
        }
        if (empty($modificationType)) {
            return ResultGenerate::Error('Пустой тип');
        }
        if (empty($modificationValue)) {
            return ResultGenerate::Error('Пустое значение');
        }

        Modifications::create([
            'title' => $title,
            'type_id' => $modificationType,
            'value' => $modificationValue,
        ]);
        return ResultGenerate::Success();
    }

    public function GetAllModifications()
    {
        return ResultGenerate::Success('', Modifications::all());
    }
}
