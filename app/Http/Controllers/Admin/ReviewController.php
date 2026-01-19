<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = Post::query()
            ->with(['author','categories','featuredImage'])
            ->withCount('categories')
            ->where('status', 'review');

        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('title', 'like', '%' . $q . '%')
                    ->orWhere('slug', 'like', '%' . $q . '%');
            });
        }

        $posts = $query->orderByDesc('updated_at')->paginate(10)->withQueryString();

        return view('admin.review.index', [
            'posts' => $posts,
            'q' => $q,
        ]);
    }

    public function publish(Post $post)
    {
        if ($post->status !== 'review') {
            return back()->with('toast', [
                'tone' => 'danger',
                'title' => 'Error',
                'message' => 'Only posts in review can be quick published.'
            ]);
        }

        $post->status = 'published';
        if (!$post->published_at) {
            $post->published_at = now();
        }
        $post->save();

        return back()->with('toast', [
            'tone' => 'success',
            'title' => 'Published',
            'message' => 'Post has been published successfully.'
        ]);
    }
}
