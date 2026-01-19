<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Login · Mini CMS</title>

  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>

  <script>tailwind.config = { theme: { extend: { colors: { primary: "#2563EB" } } } };</script>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-slate-50 text-slate-800 font-sans">
  <div class="w-full max-w-md bg-white border border-slate-200 rounded-xl shadow-sm p-6 sm:p-8">
    <div class="flex items-center gap-2 mb-6">
      <span class="material-icons-outlined text-primary" aria-hidden="true">all_inclusive</span>
      <div>
        <div class="text-lg font-bold">PointOne</div>
        <div class="text-sm text-slate-500">Mini CMS</div>
      </div>
    </div>

    <h1 class="text-xl font-semibold">Sign in</h1>
    <p class="text-sm text-slate-500 mt-1">Enter your account to continue.</p>

    @if ($errors->any())
      <div class="mt-4 p-3 rounded-lg border border-red-200 bg-red-50 text-sm text-red-700">
        {{ $errors->first() }}
      </div>
    @endif

    <form class="mt-6 space-y-4" method="POST" action="{{ url('/login') }}">
      @csrf
      <div>
        <label class="text-sm font-medium" for="email">Email</label>
        <input id="email" name="email" value="{{ old('email') }}" type="email"
               class="mt-1 w-full rounded-lg border-slate-200 focus:ring-primary/20 focus:border-primary" required/>
      </div>
      <div>
        <label class="text-sm font-medium" for="password">Password</label>
        <input id="password" name="password" type="password"
               class="mt-1 w-full rounded-lg border-slate-200 focus:ring-primary/20 focus:border-primary" required/>
      </div>
      <label class="inline-flex items-center gap-2 text-sm text-slate-600">
        <input name="remember" type="checkbox" class="h-4 w-4 rounded-[4px] border-slate-300 text-primary focus:ring-primary/30"/>
        Remember me
      </label>
      <button class="w-full bg-primary hover:bg-blue-700 text-white rounded-lg py-2.5 font-medium">Sign in</button>
    </form>

    <div class="mt-4 text-xs text-slate-500">
      Demo admin: <b>admin@local.test</b> / <b>123456</b>
    </div>
  </div>
</body>
</html>
