<x-site.layout :title="'Contact Us · ' . (setting('site_name') ?? 'PointOne')">
  
  <div class="bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-16 sm:py-24">
      <div class="grid md:grid-cols-2 gap-16 items-start">
        
        <!-- Left: Context -->
        <div>
          <h1 class="text-4xl sm:text-5xl font-bold text-slate-900 tracking-tight mb-6">Let's talk.</h1>
          <p class="text-xl text-slate-500 font-light leading-relaxed mb-8">
            Tell us about your project, your team, or just say hello. We read every message and usually respond within 24 hours.
          </p>

          <div class="space-y-6">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-primary mt-1">
                <span class="material-icons-outlined">email</span>
              </div>
              <div>
                <div class="font-bold text-slate-900">Email</div>
                <div class="text-slate-600">hello@example.com</div>
              </div>
            </div>
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-primary mt-1">
                <span class="material-icons-outlined">place</span>
              </div>
              <div>
                <div class="font-bold text-slate-900">Office</div>
                <div class="text-slate-600">123 Innovation Dr.<br>San Francisco, CA 94103</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Form -->
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8">
          <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid sm:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Jane Doe" required
                       class="w-full px-4 py-3 rounded-lg border-slate-200 bg-slate-50 focus:bg-white focus:border-primary focus:ring-primary transition shadow-sm placeholder-slate-400 text-sm">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="jane@company.com" required
                       class="w-full px-4 py-3 rounded-lg border-slate-200 bg-slate-50 focus:bg-white focus:border-primary focus:ring-primary transition shadow-sm placeholder-slate-400 text-sm">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Phone <span class="text-slate-400 font-normal">(Optional)</span></label>
              <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000"
                     class="w-full px-4 py-3 rounded-lg border-slate-200 bg-slate-50 focus:bg-white focus:border-primary focus:ring-primary transition shadow-sm placeholder-slate-400 text-sm">
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Message</label>
              <textarea name="message" rows="5" placeholder="How can we help you?" required
                        class="w-full px-4 py-3 rounded-lg border-slate-200 bg-slate-50 focus:bg-white focus:border-primary focus:ring-primary transition shadow-sm placeholder-slate-400 text-sm resize-none">{{ old('message') }}</textarea>
              @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full py-4 px-6 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
              Send Message
            </button>
            
            <p class="text-center text-xs text-slate-400">
              By submitting this form, you agree to our <a href="#" class="underline hover:text-slate-600">Privacy Policy</a>.
            </p>
          </form>
        </div>

      </div>
    </div>
  </div>
</x-site.layout>
