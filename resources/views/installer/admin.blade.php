@component('installer.layout', ['currentStep' => 3])

<h2 class="text-2xl font-bold text-gray-900 mb-2">Create Admin Account</h2>
<p class="text-gray-600 mb-6">Set up your administrator account and site details.</p>

@if($errors->has('installation'))
  <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 text-sm mb-6">
    {{ $errors->first('installation') }}
  </div>
@endif

<form method="POST" action="{{ route('installer.admin.store') }}" class="space-y-4">
  @csrf

  <div class="border-b border-gray-200 pb-4 mb-4">
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Site Settings</h3>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
    <input type="text" name="site_name" value="{{ old('site_name', 'My Website') }}" 
           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Site URL</label>
    <input type="url" name="site_url" value="{{ old('site_url', url('/')) }}" placeholder="https://example.com"
           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
  </div>

  <div class="border-b border-gray-200 pb-4 mb-4 mt-6">
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Admin Account</h3>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
    <input type="text" name="name" value="{{ old('name') }}" placeholder="John Doe"
           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
    <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com"
           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
    @error('email')
      <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
    @enderror
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
      <input type="password" name="password" 
             class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" required minlength="6">
      @error('password')
        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
      @enderror
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
      <input type="password" name="password_confirmation" 
             class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
    </div>
  </div>

  <div class="pt-4 flex gap-3">
    <a href="{{ route('installer.database') }}" 
       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
      Back
    </a>
    <button type="submit" 
            class="flex-1 bg-primary hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
      <span class="material-icons-outlined text-sm align-middle mr-1">rocket_launch</span>
      Install Mini CMS
    </button>
  </div>
</form>

@endcomponent
