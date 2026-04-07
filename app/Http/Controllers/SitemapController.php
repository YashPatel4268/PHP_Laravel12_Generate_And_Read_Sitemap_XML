<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Display dynamic sitemap (with caching)
     */
    public function index()
    {
        // Cache posts for 60 minutes
        $posts = Cache::remember('sitemap_posts', 60, function () {
            return Post::orderBy('updated_at', 'DESC')->get();
        });

        return response()->view('sitemap.index', compact('posts'))
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate static sitemap.xml file in public folder
     */
    public function generateFile()
    {
        $posts = Post::latest()->get();

        // Render XML view
        $xml = view('sitemap.index', compact('posts'))->render();

        // Save file in public folder
        file_put_contents(public_path('sitemap.xml'), $xml);

        return response()->json([
            'status' => true,
            'message' => 'Sitemap file generated successfully!'
        ]);
    }

    /**
     * Clear sitemap cache manually
     */
    public function clearCache()
    {
        Cache::forget('sitemap_posts');

        return response()->json([
            'status' => true,
            'message' => 'Sitemap cache cleared successfully!'
        ]);
    }
}
