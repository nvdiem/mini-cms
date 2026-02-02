<x-admin.layout :title="'Users · Mini CMS'" :crumb="'Users'">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-white tracking-tight">User Management</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Manage system users and their roles.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <span class="material-icons-outlined text-[18px] mr-1">add</span>
            New User
        </a>
    </div>

    <!-- Main Card -->
    <div class="card overflow-hidden">
        <!-- Toolbar -->
        <div class="px-4 sm:px-6 py-3 bg-slate-50/70 dark:bg-slate-800/30 border-b border-border-light dark:border-border-dark">
            <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-center">
                <div class="relative w-full sm:w-80">
                     <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name or email..." class="input pr-10 focus:outline-none focus:ring-2 focus:ring-slate-200 focus:border-slate-300">
                     <span class="material-icons-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">search</span>
                </div>
                 <div class="flex gap-2 w-full sm:w-auto">
                    <button class="btn bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex-1 sm:flex-none focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-200 focus:border-slate-300" type="submit">Search</button>
                    @if(request('q'))
                        <a href="{{ route('admin.users.index') }}" class="px-2 py-1 rounded-md text-sm text-slate-600 hover:text-slate-900 hover:underline dark:text-slate-400 dark:hover:text-slate-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-200 transition-colors">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        @if($users->isEmpty())
             <div class="py-14 text-center">
                <div class="mx-auto h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center">
                    <span class="material-icons-outlined text-primary" aria-hidden="true">group_off</span>
                </div>
                <h2 class="mt-4 text-sm font-semibold text-slate-900 dark:text-white">No users found</h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Try adjusting your search terms.</p>
            </div>
        @else
            <!-- Table (Desktop) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="bg-slate-50/70 dark:bg-slate-800/40 border-b border-border-light dark:border-border-dark">
                            <th class="th">User</th>
                            <th class="th">Role</th>
                            <th class="th">Status</th>
                            <th class="th">Created</th>
                            <th class="th text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-light dark:divide-border-dark">
                         @foreach($users as $user)
                            <tr class="row hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="td">
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</div>
                                </td>
                                <td class="td">
                                     @if($user->role === 'admin')
                                        <span class="badge badge-primary">Admin</span>
                                    @else
                                        <span class="badge badge-draft">Editor</span>
                                    @endif
                                </td>
                                <td class="td">
                                     @if($user->is_active)
                                        <span class="badge badge-pub">Active</span>
                                    @else
                                        <span class="badge badge-danger">Disabled</span>
                                    @endif
                                </td>
                                <td class="td text-slate-500 dark:text-slate-400">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="td text-right">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-sm btn-ghost">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
             <div class="sm:hidden p-4 space-y-3">
                @foreach($users as $user)
                    <div class="card p-4">
                        <div class="flex justify-between items-start">
                             <div>
                                <div class="font-medium text-slate-900 dark:text-white">{{ $user->name }}</div>
                                <div class="text-sm text-slate-500 dark:text-slate-400 mb-2">{{ $user->email }}</div>
                                <div class="flex gap-2">
                                     @if($user->role === 'admin')
                                        <span class="badge badge-primary">Admin</span>
                                    @else
                                        <span class="badge badge-draft">Editor</span>
                                    @endif
                                    @if($user->is_active)
                                        <span class="badge badge-pub">Active</span>
                                    @else
                                        <span class="badge badge-danger">Disabled</span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300">
                                <span class="material-icons-outlined">edit</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between px-6 py-4 border-t border-border-light dark:border-border-dark bg-slate-50/70 dark:bg-slate-800/30">
              <div class="text-xs text-slate-500 dark:text-slate-400">
                Showing <span class="font-medium">{{ $users->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $users->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $users->total() }}</span> results
              </div>
              <div class="text-sm">{{ $users->links() }}</div>
            </div>
        @endif
    </div>
</x-admin.layout>
