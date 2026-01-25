@component('installer.layout', ['currentStep' => 4])

<div class="text-center">
  <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
    <span class="material-icons-outlined text-4xl text-green-600">check_circle</span>
  </div>

  <h2 class="text-2xl font-bold text-gray-900 mb-2">Installation Complete!</h2>
  <p class="text-gray-600 mb-8">Mini CMS has been successfully installed on your server.</p>

  <div class="bg-gray-50 rounded-lg p-6 text-left mb-8">
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Your Credentials</h3>
    
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <span class="text-gray-600">Admin URL:</span>
        <code class="bg-gray-200 px-2 py-1 rounded text-sm">{{ $credentials['site_url'] }}/admin</code>
      </div>
      <div class="flex items-center justify-between">
        <span class="text-gray-600">Email:</span>
        <code class="bg-gray-200 px-2 py-1 rounded text-sm">{{ $credentials['email'] }}</code>
      </div>
      <div class="flex items-center justify-between">
        <span class="text-gray-600">Password:</span>
        <span class="text-gray-500 text-sm italic">The password you entered</span>
      </div>
    </div>
  </div>

  <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-amber-800 text-sm mb-8 text-left">
    <div class="flex items-start gap-2">
      <span class="material-icons-outlined text-amber-600">warning</span>
      <div>
        <strong>Security Note:</strong> The installer is now disabled. To reinstall, delete the file 
        <code class="bg-amber-100 px-1 rounded">storage/installed</code> and clear the database.
      </div>
    </div>
  </div>

  <div class="flex gap-3">
    <a href="{{ $credentials['site_url'] }}" target="_blank"
       class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
      <span class="material-icons-outlined text-sm align-middle mr-1">language</span>
      View Site
    </a>
    <a href="{{ $credentials['site_url'] }}/admin" 
       class="flex-1 bg-primary hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition text-center">
      <span class="material-icons-outlined text-sm align-middle mr-1">dashboard</span>
      Go to Admin
    </a>
  </div>
</div>

@endcomponent
