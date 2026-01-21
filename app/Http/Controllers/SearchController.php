<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->input('q'));
        
        if (empty($query)) {
            return view('site.search.index', [
                'posts' => collect([]),
                'q' => '',
                'total' => 0
            ]);
        }

        // Search in published posts only
        $posts = Post::where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('published_at', '<=', now())
                  ->orWhereNull('published_at');
            })
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            // Simple relevance sort: Title match first, then recent
            ->orderByRaw("CASE WHEN title LIKE ? THEN 1 ELSE 2 END", ["%{$query}%"])
            ->orderBy('published_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Highlighting Logic
        $posts->getCollection()->transform(function ($post) use ($query) {
            $post->highlighted_title = $this->highlight($post->title, $query);
            $post->search_snippet = $this->generateSnippet($post, $query);
            return $post;
        });

        return view('site.search.index', [
            'posts' => $posts,
            'q' => $query,
            'total' => $posts->total()
        ]);
    }

    /**
     * Generate a snippet with the keyword highlighted.
     * Priority: Excerpt -> Content.
     */
    private function generateSnippet($post, $query)
    {
        $text = $post->excerpt;
        
        // If excerpt is empty or doesn't contain query, try content
        if (empty($text) || stripos($text, $query) === false) {
            $text = strip_tags($post->content);
        }

        // Find keyword position
        $pos = stripos($text, $query);
        $start = max(0, $pos - 60);
        $length = 200;

        // Cut text around keyword
        $snippet = mb_substr($text, $start, $length);
        
        // Add ellipsis if needed
        if ($start > 0) $snippet = '...' . $snippet;
        if ($start + $length < mb_strlen($text)) $snippet .= '...';

        return $this->highlight($snippet, $query);
    }

    /**
     * Wrap keyword in <mark> tag (styled with Tailwind class).
     */
    private function highlight($text, $query)
    {
        return preg_replace(
            "/(" . preg_quote($query, '/') . ")/i",
            '<span class="bg-yellow-100 text-yellow-800 font-medium px-0.5 rounded">$1</span>',
            e($text) // Escape everything else first for safety
        );
    }
}
