<?php

namespace App\Http\Controllers;

use App\Models\Post;

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
            ->with(['author','featuredImage','categories'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('site.post', ['post' => $post, 'isPreview' => false, 'backUrl' => null]);
    }
}
