<x-admin.layout crumb="Dashboard" title="Dashboard">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Dashboard</h1>
            <p class="text-slate-500 text-sm mt-1">Overview of your site's performance.</p>
        </div>

        <div class="flex items-center bg-white border border-slate-200 rounded-lg p-1 shadow-sm">
            @foreach([7, 14, 30] as $r)
                <a href="{{ route('admin.dashboard', ['range' => $r]) }}" 
                   class="px-3 py-1.5 text-xs font-semibold rounded-md transition {{ $range == $r ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">
                    {{ $r }} Days
                </a>
            @endforeach
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-8">
        <!-- Views -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm transition hover:shadow-soft2 hover:border-blue-200 group">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Views</div>
            <div class="text-2xl font-bold text-slate-800 group-hover:text-blue-600 transition">{{ number_format($stats['views_total']) }}</div>
            <div class="text-[10px] text-slate-400 mt-1">Last {{ $range }} days</div>
        </div>

        <!-- Leads -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm transition hover:shadow-soft2 hover:border-blue-200 group">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">New Leads</div>
            <div class="text-2xl font-bold text-slate-800 group-hover:text-blue-600 transition">{{ number_format($stats['leads_total']) }}</div>
            <div class="text-[10px] text-slate-400 mt-1">Last {{ $range }} days</div>
        </div>

        <!-- Published -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Published</div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['posts_published']) }}</div>
            <div class="text-[10px] text-slate-400 mt-1">Total Posts</div>
        </div>

        <!-- Drafts -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Drafts</div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['posts_draft']) }}</div>
            <div class="text-[10px] text-slate-400 mt-1">Total Posts</div>
        </div>

        <!-- Review -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm {{ $stats['posts_review'] > 0 ? 'border-amber-200 bg-amber-50/30' : '' }}">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">In Review</div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['posts_review']) }}</div>
            <div class="text-[10px] text-slate-400 mt-1">Needs Action</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Main Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="font-bold text-slate-800 mb-6">Traffic & Leads</h3>
            <div class="relative w-full h-72">
                <canvas id="mainChart"></canvas>
            </div>
        </div>

        <!-- Quick Actions & Activity -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-indigo-900 rounded-xl shadow-soft2 p-6 text-white relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <h3 class="font-bold mb-4 relative z-10">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-3 relative z-10">
                    <a href="{{ route('admin.posts.create') }}" class="flex flex-col items-center justify-center p-3 bg-white/10 rounded-lg hover:bg-white/20 transition backdrop-blur-sm border border-white/10">
                        <span class="material-icons-outlined mb-1">add_circle</span>
                        <span class="text-xs font-medium">New Post</span>
                    </a>
                    <a href="{{ route('admin.pages.create') }}" class="flex flex-col items-center justify-center p-3 bg-white/10 rounded-lg hover:bg-white/20 transition backdrop-blur-sm border border-white/10">
                        <span class="material-icons-outlined mb-1">description</span>
                        <span class="text-xs font-medium">New Page</span>
                    </a>
                    <a href="{{ route('admin.review.index') }}" class="flex flex-col items-center justify-center p-3 bg-white/10 rounded-lg hover:bg-white/20 transition backdrop-blur-sm border border-white/10">
                        <span class="material-icons-outlined mb-1">rate_review</span>
                        <span class="text-xs font-medium">Review</span>
                    </a>
                    <a href="{{ route('admin.leads.index') }}" class="flex flex-col items-center justify-center p-3 bg-white/10 rounded-lg hover:bg-white/20 transition backdrop-blur-sm border border-white/10">
                        <span class="material-icons-outlined mb-1">mail</span>
                        <span class="text-xs font-medium">Leads</span>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 mb-4">Recent Activity</h3>
                <div class="relative pl-4 border-l border-slate-100 space-y-6">
                    @forelse($activities as $log)
                        <div class="relative">
                            <div class="absolute -left-[21px] top-1.5 w-2.5 h-2.5 rounded-full bg-slate-200 ring-4 ring-white"></div>
                            <div class="text-sm text-slate-800 font-medium">
                                @if(!empty($log->meta['url']))
                                    <a href="{{ $log->meta['url'] }}" class="hover:text-blue-600 hover:underline transition">
                                        {{ $log->message }}
                                    </a>
                                @else
                                    {{ $log->message }}
                                @endif
                            </div>
                            <div class="text-xs text-slate-400 mt-0.5">
                                {{ $log->created_at->diffForHumans() }} 
                                @if($log->user) &middot; {{ $log->user->name }} @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-400 italic">No activity recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Top Posts -->
    <div class="mt-8 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 font-bold text-slate-800">
            Top Content (Last {{ $range }} Days)
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Post</th>
                        <th class="px-6 py-3 font-semibold w-32 text-center">Views</th>
                        <th class="px-6 py-3 font-semibold w-32">Published</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($topPosts as $post)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="px-6 py-3">
                                <a href="{{ route('admin.posts.edit', $post->id) }}" class="font-medium text-slate-900 hover:text-blue-600 block line-clamp-1">
                                    {{ $post->title }}
                                </a>
                                <div class="text-xs text-slate-400 mt-0.5 flex gap-2">
                                    @if($post->status === 'published')
                                        <span class="text-emerald-600">Published</span>
                                    @else
                                        <span class="text-slate-500">{{ ucfirst($post->status) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded bg-slate-100 text-slate-700 font-bold text-xs group-hover:bg-blue-50 group-hover:text-blue-700 transition">
                                    {{ number_format($post->views_count) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm text-slate-500">
                                {{ optional($post->published_at)->format('M d') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-400">
                                No views data available yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('mainChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: 'Views',
                            data: @json($chartData['views']),
                            borderColor: '#3b82f6', // blue-500
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointRadius: 0,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Leads',
                            data: @json($chartData['leads']),
                            borderColor: '#8b5cf6', // violet-500
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            tension: 0.3,
                            fill: false,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end',
                            labels: { usePointStyle: true, boxWidth: 8 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [2, 4], color: '#f1f5f9' },
                            ticks: { font: { size: 10 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { maxTicksLimit: 7, font: { size: 10 } }
                        }
                    }
                }
            });
        });
    </script>
</x-admin.layout>
