<x-admin.layout :title="'New Post · Mini CMS'" :crumb="'Post Editor'">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight">New Post</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Create a new post.</p>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.posts.store') }}">
    @csrf
    @include('admin.posts._form')
  </form>
</x-admin.layout>
