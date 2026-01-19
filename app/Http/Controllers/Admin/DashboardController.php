<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Page;
use App\Models\Lead;
use App\Models\PostViewStat;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->input('range', 14); // Default 14 days
        $startDate = Carbon::today()->subDays($range - 1);
        $dates = [];
        for ($i = 0; $i < $range; $i++) {
            $dates[] = Carbon::today()->subDays($range - 1 - $i)->format('Y-m-d');
        }

        // --- 1. KPI Cards ---
        $stats = [
            'views_total' => PostViewStat::where('date', '>=', $startDate)->sum('views'),
            'leads_total' => Lead::whereDate('created_at', '>=', $startDate)->count(),
            'posts_published' => Post::where('status', 'published')->count(),
            'posts_draft' => Post::where('status', 'draft')->count(),
            'posts_review' => Post::where('status', 'review')->count(),
        ];

        // --- 2. Chart Data ---
        // Views
        $dailyViews = PostViewStat::where('date', '>=', $startDate)
            ->selectRaw('date, sum(views) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        // Leads
        $dailyLeads = Lead::whereDate('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, count(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $chartData = [
            'labels' => [],
            'views' => [],
            'leads' => [],
        ];

        foreach ($dates as $date) {
            $formattedDate = Carbon::parse($date)->format('M d');
            $chartData['labels'][] = $formattedDate;
            $chartData['views'][] = $dailyViews[$date] ?? 0;
            $chartData['leads'][] = $dailyLeads[$date] ?? 0;
        }

        // --- 3. Top Content ---
        $topPosts = Post::published()
            ->join('post_view_stats', 'posts.id', '=', 'post_view_stats.post_id')
            ->where('post_view_stats.date', '>=', $startDate)
            ->selectRaw('posts.*, sum(post_view_stats.views) as views_count')
            ->groupBy('posts.id')
            ->orderByDesc('views_count')
            ->limit(10)
            ->get();

        // --- 4. Recent Activity ---
        $activities = ActivityLog::with(['user', 'subject'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'chartData', 'topPosts', 'activities', 'range'));
    }
}
