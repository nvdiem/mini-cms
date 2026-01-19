<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Page;

class SiteController extends Controller
{
    public function home()
    {
        $posts = Post::query()
            ->published()
            ->with(['author','featuredImage','categories','tags'])
            ->orderByDesc('published_at')
            ->orderByDesc('updated_at')
            ->paginate(setting('posts_per_page', 10));

        // Featured post is the first one
        $featuredPost = $posts->first();
        
        return view('site.home', compact('posts', 'featuredPost'));
    }

    public function show(string $slug)
    {
        $post = Post::query()
            ->published()
            ->with(['author','featuredImage','categories','tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Prev/Next navigation
        $nextPost = null;
        $prevPost = null;

        if ($post->published_at) {
            $nextPost = Post::published()
                ->where('published_at', '>', $post->published_at)
                ->orderBy('published_at', 'asc')
                ->first();

            $prevPost = Post::published()
                ->where('published_at', '<', $post->published_at)
                ->orderByDesc('published_at')
                ->first();
        }

        // Related posts (by tag or category)
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->where(function($q) use ($post) {
                $tagIds = $post->tags->pluck('id')->toArray();
                $catIds = $post->categories->pluck('id')->toArray();
                
                if (!empty($tagIds)) {
                    $q->whereHas('tags', function($t) use ($tagIds) {
                        $t->whereIn('tags.id', $tagIds);
                    });
                }
                
                if (!empty($catIds)) {
                    $q->orWhereHas('categories', function($c) use ($catIds) {
                         $c->whereIn('categories.id', $catIds);
                    });
                }
            })
            ->limit(3)
            ->get();

        return view('site.post', [
            'post' => $post,
            'nextPost' => $nextPost,
            'prevPost' => $prevPost,
            'relatedPosts' => $relatedPosts,
            'isPreview' => false,
            'backUrl' => null
        ]);
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
