<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DropzoneController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = 'Dropzone example';
        return view('examples.dropzone', compact('pageTitle'));
    }

    public function upload(Request $request)
    {
        $files = $request->file('files');

        if (is_array($files)) {
            $filenames = collect([]);

            foreach ($files as $file) {
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $filenames->push("uploads/$filename");
            }

            return response()->json([
                'value' => $filenames
            ]);
        }

        $filename = Str::random(40) . '.' . $files->getClientOriginalExtension();

        return response()->json([
            'value' => "uploads/$filename"
        ]);
    }

    public function saveForm(Request $request)
    {
        Log::info("TIME: " . Carbon::now()->format('Y-m-d H:i:s'));

        return response()->json([
            "success" => true,
            "data" => $request->all()
        ]);
    }
}
