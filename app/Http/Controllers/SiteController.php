<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Page;

use App\Models\PostViewStat;

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

        // Increment Views
        PostViewStat::incrementFor($post->id);

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

        // --- 1. Breadcrumbs ---
        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('site.home')],
            ['label' => 'Blog', 'url' => route('site.home') . '#latest'],
        ];
        if ($post->categories->isNotEmpty()) {
            // Optional: link to category if category page exists, for now just text or generic link
            $cat = $post->categories->first();
            $breadcrumbs[] = ['label' => $cat->name, 'url' => null];
        }
        $breadcrumbs[] = ['label' => $post->title, 'url' => null];


        // --- 2. Advanced Related Posts ---
        // Score: Tag match = 3, Category match = 1
        $tagIds = $post->tags->pluck('id')->toArray();
        $catIds = $post->categories->pluck('id')->toArray();

        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->where(function($q) use ($tagIds, $catIds) {
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
            ->with(['featuredImage','categories']) // Eager load
            ->limit(10) // Get candidates
            ->get();

        // Sort by score loosely for now (since SQL scoring is complex without raw)
        // Simple fallback: If count < 4, fetch latest posts
        if ($relatedPosts->count() < 4) {
            $needed = 4 - $relatedPosts->count();
            $excludeIds = $relatedPosts->pluck('id')->push($post->id)->toArray();
            
            $fallback = Post::published()
                ->whereNotIn('id', $excludeIds)
                ->with(['featuredImage','categories'])
                ->orderByDesc('published_at')
                ->limit($needed)
                ->get();
                
            $relatedPosts = $relatedPosts->merge($fallback);
        }
        
        $relatedPosts = $relatedPosts->take(4);


        // --- 3. Schema.org (Article) ---
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $post->meta_description ?? Str::limit(strip_tags($post->content), 160),
            'image' => $post->featuredImage ? $post->featuredImage->url() : null,
            'datePublished' => $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->author->name ?? setting('site_name')
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => setting('site_name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('favicon.ico') // Simplified
                ]
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('site.posts.show', $post->slug)
            ]
        ];

        return view('site.post', [
            'post' => $post,
            'nextPost' => $nextPost,
            'prevPost' => $prevPost,
            'relatedPosts' => $relatedPosts,
            'breadcrumbs' => $breadcrumbs,
            'schema' => $schema,
            'isPreview' => false,
            'backUrl' => null,
            'canonical' => route('site.posts.show', $post->slug)
        ]);
    }

    public function showPage(string $slug)
    {
        $page = Page::query()
            ->published()
            ->with(['author','featuredImage'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('site.home')],
            ['label' => $page->title, 'url' => null],
        ];

        // Schema.org (WebPage)
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $page->title,
            'description' => $page->meta_description ?? Str::limit(strip_tags($page->content), 160),
            'url' => route('site.pages.show', $page->slug),
            'dateModified' => $page->updated_at->toIso8601String(),
            'publisher' => [
                '@type' => 'Organization',
                'name' => setting('site_name')
            ]
        ];

        return view('site.page', [
            'page' => $page, 
            'breadcrumbs' => $breadcrumbs,
            'schema' => $schema,
            'isPreview' => false, 
            'backUrl' => null,
            'canonical' => route('site.pages.show', $page->slug)
        ]);
    }
}
