@extends('layouts.frontend')
@section('title', 'Contact Us - Newsentric | Global AI News')

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold text-slate-900 mb-4">Get in Touch</h1>
        <p class="text-lg text-slate-600">Have a tip, a question, or a partnership inquiry? Our global team is here to help.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                
               @if(session('success'))
    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2 transition-all duration-500">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
        {{ session('success') }}
    </div>
@endif

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name</label>
                    <input type="text" name="name" placeholder="John Doe" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                    <input type="email" name="email" placeholder="john@example.com" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Your Message</label>
                    <textarea name="message" rows="5" placeholder="How can we help you today?" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" required></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                    Send Message
                </button>
            </form>
        </div>

        <div class="flex flex-col justify-center">
            <div class="mb-10">
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Our Headquarters</h3>
                <p class="text-slate-600 text-lg leading-relaxed">
                   Sangam Vihar,Gopalganj,Bihar,India
                   
                </p>
            </div>

           <div class="mb-8">
    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
        </svg>
    </div>
    <h3 class="text-xl font-bold text-slate-900 mb-2">Email Support</h3>
    
    <a href="mailto:newsentric@gmail.com?subject=Inquiry%20from%20Website" class="text-blue-600 text-lg font-semibold break-all hover:text-blue-800 transition-colors duration-200">
        newsentric@gmail.com
    </a>
    
    <p class="text-slate-500 text-sm mt-1">We typically respond within 24 hours.</p>
</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('success-alert');
        if (alert) {
            // 5 seconds wait karega, fir gayab hoga
            setTimeout(function() {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                
                // Animating out smoothly
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500); // 0.5s transition time
            }, 5000); 
        }
    });
</script>
@endpush