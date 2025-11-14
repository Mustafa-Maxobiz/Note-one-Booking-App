<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class StorageController extends Controller
{
    /**
     * Serve storage files without /public/ prefix
     */
    public function serve($path)
    {
        // Security: Only allow access to specific directories
        $allowedPaths = ['profile-pictures', 'documents', 'uploads'];
        $firstSegment = explode('/', $path)[0] ?? '';
        
        if (!in_array($firstSegment, $allowedPaths)) {
            abort(403, 'Access denied to this directory');
        }
        
        // Use the correct storage path
        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath) || !is_file($fullPath)) {
            abort(404, 'File not found');
        }
        
        // Get file info
        $mimeType = mime_content_type($fullPath);
        $fileSize = filesize($fullPath);
        
        // Set proper headers
        $headers = [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Cache-Control' => 'public, max-age=31536000',
            'Access-Control-Allow-Origin' => '*',
        ];
        
        // For images, add additional headers
        if (str_starts_with($mimeType, 'image/')) {
            $headers['Content-Disposition'] = 'inline';
        }
        
        // Return the file
        return response()->file($fullPath, $headers);
    }
}
