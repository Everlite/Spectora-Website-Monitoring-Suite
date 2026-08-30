@if (session('error'))
    <div class="bg-studio-rose/15 border border-studio-rose/30 text-studio-rose px-4 py-3 rounded-studio-sm text-xs font-bold" role="alert">
        {{ session('error') }}
    </div>
@endif

@if (session('status'))
    <div class="bg-studio-emerald/15 border border-studio-emerald/30 text-studio-emerald px-4 py-3 rounded-studio-sm text-xs font-bold" role="alert">
        {{ session('status') }}
    </div>
@endif
