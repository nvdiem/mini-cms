<x-admin.layout title="Edit User">
    <div class="max-w-xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.users.index') }}" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-700 hover:border-slate-300 transition shadow-sm">
                <span class="material-icons-outlined text-[16px]">arrow_back</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-800">Edit User: {{ $user->name }}</h1>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Name -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-0 transition">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-0 transition">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Role</label>
                    <select name="role" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-0 transition">
                        <option value="editor" {{ old('role', $user->role) == 'editor' ? 'selected' : '' }}>Editor</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <!-- Active Status -->
                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} 
                           @if($user->id === auth()->id()) disabled @endif
                           class="w-5 h-5 text-slate-900 border-slate-300 rounded focus:ring-slate-900 disabled:opacity-50">
                    <div>
                        <label for="is_active" class="font-medium text-slate-900 select-none cursor-pointer @if($user->id === auth()->id()) opacity-50 @endif">Active Account</label>
                        @if($user->id === auth()->id())
                            <p class="text-slate-400 text-xs">You cannot disable your own account.</p>
                            <!-- Hidden input to ensure value is sent even if unchecked visually (disabled inputs don't submit) but here we force checked logic via controller anyway -->
                        @endif
                    </div>
                </div>

                <!-- Password Change Section -->
                <div class="pt-6 border-t border-slate-100">
                    <h3 class="font-bold text-slate-800 mb-4">Change Password</h3>
                    <div class="space-y-4">
                        <input type="password" name="password" placeholder="New Password (leave blank to keep current)" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-0 transition placeholder-slate-400">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="btn-primary">
                        Update User
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Toggle Status Button (Optional shortcut, kept separate for clarity) -->
        @if($user->id !== auth()->id())
        <div class="flex justify-center">
             <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                 @csrf
                 <button type="submit" class="text-sm text-slate-400 hover:text-red-500 font-medium underline">
                     {{ $user->is_active ? 'Disable this user' : 'Enable this user' }}
                 </button>
             </form>
        </div>
        @endif
    </div>
</x-admin.layout>
