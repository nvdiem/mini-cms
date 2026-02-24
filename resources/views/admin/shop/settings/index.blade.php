<x-admin.layout :title="'Shop Settings · Mini CMS'" :crumb="'Shop Settings'">
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-text-strong dark:text-white">Shop Settings</h1>
    <p class="text-sm text-text-muted mt-1">Configure your shop</p>
  </div>

  <form method="POST" action="{{ route('admin.shop.settings.update') }}" class="card p-6 max-w-2xl">
    @csrf

    <div class="space-y-5">
      @foreach($settings as $key => $meta)
        @php $formKey = str_replace('.', '_', $key); @endphp
        <div>
          <label class="block text-sm font-medium mb-2">{{ $meta['label'] }}</label>
          @if($meta['type'] === 'textarea')
            <textarea name="{{ $formKey }}" rows="3" class="input">{{ old($formKey, $meta['value']) }}</textarea>
          @elseif($meta['type'] === 'number')
            <input type="number" name="{{ $formKey }}" value="{{ old($formKey, $meta['value']) }}" class="input" min="0" step="1000">
          @else
            <input type="text" name="{{ $formKey }}" value="{{ old($formKey, $meta['value']) }}" class="input">
          @endif
          @error($formKey) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
      @endforeach
    </div>

    <div class="mt-6 flex justify-end">
      <button type="submit" class="btn-primary">
        <span class="material-icons-outlined text-[18px]">save</span> Save Settings
      </button>
    </div>
  </form>
</x-admin.layout>
