<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>Install · Mini CMS</title>

  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#2563eb',
          },
          fontFamily: { sans: ['Inter', 'sans-serif'] },
        }
      }
    };
  </script>

  <style>
    body { 
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
      min-height: 100vh;
    }
  </style>
</head>

<body class="font-sans antialiased">
  <div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 text-white">
          <span class="material-icons-outlined text-4xl">all_inclusive</span>
          <span class="text-3xl font-bold tracking-tight">Mini CMS</span>
        </div>
        <p class="text-white/80 mt-2">Installation Wizard</p>
      </div>

      <!-- Progress Steps -->
      <div class="flex items-center justify-center gap-2 mb-8">
        @php
          $steps = ['Requirements', 'Database', 'Admin', 'Complete'];
          $currentStep = $currentStep ?? 1;
        @endphp
        @foreach($steps as $index => $step)
          <div class="flex items-center">
            <div class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium
              {{ $index + 1 < $currentStep ? 'bg-green-500 text-white' : '' }}
              {{ $index + 1 == $currentStep ? 'bg-white text-primary' : '' }}
              {{ $index + 1 > $currentStep ? 'bg-white/20 text-white/60' : '' }}
            ">
              @if($index + 1 < $currentStep)
                <span class="material-icons-outlined text-sm">check</span>
              @else
                {{ $index + 1 }}
              @endif
            </div>
            @if($index < count($steps) - 1)
              <div class="w-8 h-0.5 {{ $index + 1 < $currentStep ? 'bg-green-500' : 'bg-white/20' }}"></div>
            @endif
          </div>
        @endforeach
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-8">
          {{ $slot }}
        </div>
      </div>

      <!-- Footer -->
      <p class="text-center text-white/60 text-sm mt-6">
        Mini CMS v1.0.0 • Laravel {{ app()->version() }}
      </p>
    </div>
  </div>
</body>
</html>
