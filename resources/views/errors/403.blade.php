<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>403 Forbidden</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 h-screen flex flex-col items-center justify-center p-4">
  
  <div class="text-center max-w-md">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600 mb-6">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m2-5h2m-2 0v-2m0 2H8m4-6V4" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9a3 3 0 100-6 3 3 0 000 6zm0 13a9 9 0 110-18 9 9 0 010 18z" />
      </svg>
    </div>
    
    <h1 class="text-3xl font-bold text-slate-900 mb-2">Access Denied</h1>
    <p class="text-slate-500 mb-8 leading-relaxed">
      You do not have permission to view this page. This area is restricted to administrators only.
    </p>
    
    <div class="space-y-3">
      <a href="{{ route('admin.dashboard') }}" class="block w-full px-4 py-3 bg-slate-900 text-white font-semibold rounded-lg hover:bg-slate-800 transition">
        Return to Dashboard
      </a>
      <button onclick="history.back()" class="block w-full px-4 py-3 bg-white border border-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition">
        Go Back
      </button>
    </div>
  </div>

</body>
</html>
