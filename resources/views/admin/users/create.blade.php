<x-admin.layout title="Create User">
    <div class="max-w-xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.users.index') }}" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-700 hover:border-slate-300 transition shadow-sm">
                <span class="material-icons-outlined text-[16px]">arrow_back</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-800">Add New User</h1>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <!-- Name -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-0 transition">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-0 transition">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Role</label>
                    <select name="role" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-0 transition">
                        <option value="editor" {{ old('role') == 'editor' ? 'selected' : '' }}>Editor (Limited access)</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Full access)</option>
                    </select>
                    <p class="text-slate-400 text-xs mt-1">Editors cannot access Settings or User Management.</p>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" required minlength="6"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-slate-400 focus:ring-0 transition">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Active Status -->
                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} 
                           class="w-5 h-5 text-slate-900 border-slate-300 rounded focus:ring-slate-900">
                    <div>
                        <label for="is_active" class="font-medium text-slate-900 select-none cursor-pointer">Active Account</label>
                        <p class="text-slate-400 text-xs">Uncheck to disable logging in.</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="btn-primary">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin.layout>
