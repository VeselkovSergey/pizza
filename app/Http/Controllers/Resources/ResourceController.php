<?php


namespace App\Http\Controllers\Resources;


use App\Helpers\ArrayHelper;
use App\Helpers\ResultGenerate;
use App\Models\Products;
use App\Models\ProductsPrices;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ScssPhp\ScssPhp\Compiler;

class ResourceController
{
    private $ContentType = [
        'js' => 'text/javascript',
        'css' => 'text/css',
        'scss' => 'text/css',
    ];

    public function GetResources(Request $request)
    {
        if (pathinfo($request->fileName, PATHINFO_EXTENSION) === 'js') {
            return $this->JS($request);
        } else if (pathinfo($request->fileName, PATHINFO_EXTENSION) === 'css') {
            return $this->CSS($request);
        } else if (pathinfo($request->fileName, PATHINFO_EXTENSION) === 'scss') {
            return $this->SCSS($request);
        }
        return response('', '404');
    }

    private function JS($request)
    {
        return response(file_get_contents(resource_path("{$request->directory}/{$request->fileName}")))
            ->header('Content-Type', $this->ContentType[pathinfo($request->fileName, PATHINFO_EXTENSION)]);
    }

    private function CSS($request)
    {
        return response(file_get_contents(resource_path("{$request->directory}/{$request->fileName}")))
            ->header('Content-Type', $this->ContentType[pathinfo($request->fileName, PATHINFO_EXTENSION)]);
    }

    private function SCSS($request)
    {
        $compiler = new Compiler();
        $css = $compiler->compileString(file_get_contents(resource_path("{$request->directory}/{$request->fileName}")))->getCss();
        return response($css)->header('Content-Type', $this->ContentType[pathinfo($request->fileName, PATHINFO_EXTENSION)]);
    }
}
