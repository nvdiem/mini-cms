<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $posts = Post::published()->orderBy('updated_at', 'desc')->get();
        $pages = Page::published()->orderBy('updated_at', 'desc')->get();
        $latestPost = $posts->first();
        $latestPage = $pages->first();
        $latestUpdate = max($latestPost?->updated_at, $latestPage?->updated_at, now());

        $content = view('site.sitemap', compact('posts', 'pages', 'latestUpdate'))->render();
        
        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8'
        ]);
    }

    public function robots()
    {
        $content = "User-agent: *\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /login\n";
        $content .= "Disallow: /admin/*\n";
        $content .= "Sitemap: " . url('sitemap.xml') . "\n";

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8'
        ]);
    }
}
