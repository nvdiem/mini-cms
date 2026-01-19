<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Page;

class SiteController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->published()
            ->with(['author','featuredImage','categories'])
            ->orderByDesc('published_at')
            ->orderByDesc('updated_at')
            ->paginate(10);

        return view('site.home', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = Post::query()
            ->published()
            ->with(['author','featuredImage','categories','tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('site.post', ['post' => $post, 'isPreview' => false, 'backUrl' => null]);
    }

    public function showPage(string $slug)
    {
        $page = Page::query()
            ->published()
            ->with(['author','featuredImage'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('site.page', ['page' => $page, 'isPreview' => false, 'backUrl' => null]);
    }
}
