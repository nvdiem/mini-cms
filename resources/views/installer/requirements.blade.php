@component('installer.layout', ['currentStep' => 1])

<h2 class="text-2xl font-bold text-gray-900 mb-2">Server Requirements</h2>
<p class="text-gray-600 mb-6">Checking if your server meets the requirements...</p>

<div class="space-y-3 mb-8">
  @foreach($requirements as $key => $req)
    <div class="flex items-center justify-between p-3 rounded-lg {{ $req['status'] ? 'bg-green-50' : 'bg-red-50' }}">
      <div class="flex items-center gap-3">
        <span class="material-icons-outlined text-xl {{ $req['status'] ? 'text-green-600' : 'text-red-600' }}">
          {{ $req['status'] ? 'check_circle' : 'error' }}
        </span>
        <span class="font-medium text-gray-700">{{ $req['name'] }}</span>
      </div>
      <div class="text-sm {{ $req['status'] ? 'text-green-600' : 'text-red-600' }}">
        {{ $req['current'] }}
      </div>
    </div>
  @endforeach
</div>

@if($canProceed)
  <a href="{{ route('installer.database') }}" 
     class="block w-full bg-primary hover:bg-blue-700 text-white text-center font-medium py-3 px-4 rounded-lg transition">
    Continue to Database Setup
    <span class="material-icons-outlined text-sm align-middle ml-1">arrow_forward</span>
  </a>
@else
  <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 text-sm">
    <strong>Cannot proceed:</strong> Please fix the requirements marked in red before continuing.
  </div>
@endif

@endcomponent
