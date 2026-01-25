@component('installer.layout', ['currentStep' => 2])

<h2 class="text-2xl font-bold text-gray-900 mb-2">Database Configuration</h2>
<p class="text-gray-600 mb-6">Enter your MySQL database credentials.</p>

@if($errors->has('db_connection'))
  <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 text-sm mb-6">
    {{ $errors->first('db_connection') }}
  </div>
@endif

<form method="POST" action="{{ route('installer.database.store') }}" class="space-y-4">
  @csrf

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Database Host</label>
      <input type="text" name="db_host" value="{{ old('db_host', '127.0.0.1') }}" 
             class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
      <input type="number" name="db_port" value="{{ old('db_port', '3306') }}" 
             class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
    </div>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Database Name</label>
    <input type="text" name="db_database" value="{{ old('db_database') }}" placeholder="mini_cms"
           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
    <p class="text-xs text-gray-500 mt-1">Database must already exist</p>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
    <input type="text" name="db_username" value="{{ old('db_username', 'root') }}" 
           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
    <input type="password" name="db_password" value="{{ old('db_password') }}" placeholder="Leave empty if none"
           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
  </div>

  <div class="pt-4 flex gap-3">
    <a href="{{ route('installer.requirements') }}" 
       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
      Back
    </a>
    <button type="submit" 
            class="flex-1 bg-primary hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
      Test Connection & Continue
      <span class="material-icons-outlined text-sm align-middle ml-1">arrow_forward</span>
    </button>
  </div>
</form>

@endcomponent
