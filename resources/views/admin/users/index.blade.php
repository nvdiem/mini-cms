<x-admin.layout title="Settings">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-800">User Management</h1>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-slate-900 text-white font-medium rounded-lg hover:bg-slate-800 transition shadow-sm flex items-center gap-2">
            <span class="material-icons-outlined text-[18px]">add</span>
            New User
        </a>
    </div>

    <!-- Search / Filter -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-6 flex flex-col sm:flex-row gap-4 justify-between items-center">
        <form action="{{ route('admin.users.index') }}" method="GET" class="w-full sm:w-auto flex-1 max-w-md relative">
            <span class="material-icons-outlined absolute left-3 top-2.5 text-slate-400">search</span>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name or email..." 
                class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-0 transition text-sm">
        </form>
    </div>

    <!-- Table (Desktop) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden hidden md:block">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-6 py-4 font-semibold">User</th>
                    <th class="px-6 py-4 font-semibold">Role</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold">Created</th>
                    <th class="px-6 py-4 text-right font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900">{{ $user->name }}</div>
                            <div class="text-sm text-slate-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-violet-100 text-violet-700">
                                    Admin
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                    Editor
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Disabled
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-slate-400 hover:text-slate-600 font-medium text-sm transition">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-4">
        @foreach($users as $user)
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-start justify-between">
                <div>
                    <div class="font-bold text-slate-900">{{ $user->name }}</div>
                    <div class="text-sm text-slate-500 mb-2">{{ $user->email }}</div>
                    <div class="flex gap-2 mb-3">
                         @if($user->role === 'admin')
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-violet-50 text-violet-700">Admin</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600">Editor</span>
                        @endif
                         @if($user->is_active)
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700">Active</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700">Disabled</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-slate-400 hover:text-slate-600">
                    <span class="material-icons-outlined">edit</span>
                </a>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</x-admin.layout>
