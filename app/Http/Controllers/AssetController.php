<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Serve JavaScript files
     */
    public function serveJs($filename)
    {
        $filePath = public_path('js/' . $filename);
        
        if (!file_exists($filePath)) {
            abort(404);
        }
        
        $content = file_get_contents($filePath);
        $mimeType = 'application/javascript';
        
        return response($content)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
