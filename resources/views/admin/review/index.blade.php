<x-admin.layout :title="'Review Queue · Mini CMS'" :crumb="'Review Queue'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight">Review Queue</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Quick publish posts waiting for review.</p>
    </div>
  </div>

  <div class="card overflow-hidden">
    <div class="px-4 sm:px-6 py-3 bg-slate-50/70 dark:bg-slate-800/30 border-b border-border-light dark:border-border-dark">
      <form class="flex gap-3 items-center" method="GET" action="{{ route('admin.review.index') }}">
        <div class="relative w-full sm:w-80">
          <input class="input pr-10 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:border-slate-300" name="q" value="{{ $q }}" placeholder="Search posts in review..." />
          <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">search</span>
        </div>
        <button class="btn-ghost" type="submit">Search</button>
        <a class="btn-soft px-3 py-2" href="{{ route('admin.review.index') }}">Clear</a>

        <div class="sm:ml-auto text-sm text-slate-500 dark:text-slate-400">
          {{ $posts->total() }} items
        </div>
      </form>
    </div>

    @if($posts->count() === 0)
      <div class="p-10 text-center">
        <div class="mx-auto h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
          <span class="material-icons-outlined text-primary" aria-hidden="true">rate_review</span>
        </div>
        <h2 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">No posts in review</h2>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">All posts have been reviewed and published or are in draft.</p>
        <div class="mt-5">
          <a class="btn-primary" href="{{ route('admin.posts.index') }}">View All Posts</a>
        </div>
      </div>
    @else
      <div class="sm:hidden p-4 space-y-3">
        @foreach($posts as $post)
          <article class="card p-4">
            <div class="flex items-start gap-3">
              <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                  <div class="font-medium text-slate-900 dark:text-white truncate">{{ $post->title }}</div>
                  <span class="badge badge-draft">Review</span>
                </div>

                <div class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                  {{ $post->author?->name ?? '—' }}
                  · Updated {{ $post->updated_at->format('M j, Y') }}
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                  <form method="POST" action="{{ route('admin.review.publish', $post) }}" class="inline">
                    @csrf
                    <button class="btn-primary px-3 py-1.5" type="submit">
                      <span class="material-icons-outlined text-[16px]" aria-hidden="true">publish</span>
                      Quick Publish
                    </button>
                  </form>
                  <a class="btn-soft px-3 py-1.5" href="{{ route('admin.posts.edit', $post) }}">Edit</a>
                  <a class="btn-ghost px-3 py-1.5" href="{{ route('admin.posts.preview', $post) }}" target="_blank" rel="noopener">Preview</a>
                </div>
              </div>
            </div>
          </article>
        @endforeach
      </div>

      <div class="hidden sm:block overflow-x-auto">
        <table class="table">
          <thead>
            <tr class="bg-slate-50/70 dark:bg-slate-800/40 border-b border-border-light dark:border-border-dark">
              <th class="th">Title</th>
              <th class="th">Author</th>
              <th class="th">Categories</th>
              <th class="th">Updated</th>
              <th class="th text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light dark:divide-border-dark">
            @foreach($posts as $post)
              <tr class="row group">
                <td class="td">
                  <div class="flex items-center gap-3">
                    @if($post->featuredImage)
                      <img src="{{ $post->featuredImage->url() }}" alt="" class="h-9 w-12 object-cover rounded-md border border-border-light dark:border-border-dark"/>
                    @else
                      <div class="h-9 w-12 rounded-md border border-border-light dark:border-border-dark bg-slate-100 dark:bg-slate-800"></div>
                    @endif
                    <div class="min-w-0">
                      <div class="font-medium text-slate-900 dark:text-white truncate">{{ $post->title }}</div>
                      <div class="text-xs text-slate-600 dark:text-slate-400 truncate">/{{ $post->slug }}</div>
                    </div>
                  </div>
                </td>
                <td class="td text-slate-600 dark:text-slate-300">{{ $post->author?->name ?? '—' }}</td>
                <td class="td text-slate-600 dark:text-slate-300">{{ $post->categories_count ?? 0 }}</td>
                <td class="td text-slate-500 dark:text-slate-400">{{ $post->updated_at->format('M j, Y') }}</td>
                <td class="td text-right">
                  <div class="flex items-center justify-end gap-2">
                    <form method="POST" action="{{ route('admin.review.publish', $post) }}" class="inline">
                      @csrf
                      <button class="btn-primary px-3 py-1.5" type="submit">
                        <span class="material-icons-outlined text-[16px]" aria-hidden="true">publish</span>
                        Quick Publish
                      </button>
                    </form>
                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn-soft px-3 py-1.5">Edit</a>
                    <a href="{{ route('admin.posts.preview', $post) }}" target="_blank" rel="noopener" class="btn-ghost px-3 py-1.5">Preview</a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <div class="flex items-center justify-between px-6 py-4 border-t border-border-light dark:border-border-dark bg-slate-50/70 dark:bg-slate-800/30">
          <div class="text-xs text-slate-500 dark:text-slate-400">
            Showing <span class="font-medium">{{ $posts->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $posts->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $posts->total() }}</span> results
          </div>
          <div class="text-sm">{{ $posts->links() }}</div>
        </div>
      </div>
    @endif
  </div>
</x-admin.layout>
