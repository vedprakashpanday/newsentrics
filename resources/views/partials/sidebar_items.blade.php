<div class="mb-6">
    <div class="flex items-center gap-2 mb-2">
        <span class="bg-blue-100 text-blue-600 p-1.5 rounded-lg">
            <i class="bi bi-calendar-check text-sm"></i>
        </span>
        <h4 class="font-bold text-slate-800">{{ $ai_insights['history_title'] }}</h4>
    </div>
    <p class="text-sm text-slate-600 leading-relaxed border-l-2 border-blue-100 ps-3 italic">
        {{ $ai_insights['history_info'] }}
    </p>
</div>

<hr class="border-slate-100 mb-6">

<h4 style="font-weight: 600;margin-bottom:5px;">Quote Of The Day</h4>

<div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-100">
    <i class="bi bi-quote text-3xl text-blue-200 leading-none"></i>
    <p class="text-sm font-semibold text-slate-700 mt-[-10px] italic">
        "{{ $ai_insights['quote'] }}"
    </p>
</div>