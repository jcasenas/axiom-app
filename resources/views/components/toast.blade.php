@if(session('toast'))
<div id="axiom-toast" style="
    position: fixed;
    bottom: 1.25rem;
    right: 1.25rem;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    color: #fff;
    font-size: 0.875rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    background-color: {{
        session('toast.type') === 'success' ? '#16a34a' :
        (session('toast.type') === 'error'   ? '#dc2626' :
        (session('toast.type') === 'warning' ? '#d97706' : '#2563eb'))
    }};
">
    @if(session('toast.type') === 'success')
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
    @elseif(session('toast.type') === 'error')
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    @elseif(session('toast.type') === 'warning')
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
    @else
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
        </svg>
    @endif

    <span>{{ session('toast.message') }}</span>

    <button onclick="document.getElementById('axiom-toast').remove()" style="
        margin-left: 0.5rem;
        background: none;
        border: none;
        color: #fff;
        cursor: pointer;
        opacity: 0.8;
        font-size: 1rem;
        line-height: 1;
        padding: 0;
    ">✕</button>
</div>

<script>
    setTimeout(function() {
        var toast = document.getElementById('axiom-toast');
        if (toast) {
            toast.style.transition = 'opacity 0.4s ease';
            toast.style.opacity = '0';
            setTimeout(function() { toast.remove(); }, 400);
        }
    }, 4000);
</script>
@endif