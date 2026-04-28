@auth
<div style="position: relative;" id="bell-wrapper">

    {{-- Bell Button --}}
    <button onclick="toggleBell()" style="
        position: relative;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.4rem;
        color: #a8a4e0;
    " aria-label="Notifications">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span style="
                position: absolute;
                top: 2px; right: 2px;
                width: 17px; height: 17px;
                background: #dc2626;
                color: #fff;
                font-size: 10px;
                font-weight: 700;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            ">
                {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div id="bell-dropdown" style="
        display: none;
        position: absolute;
        right: 0; top: calc(100% + 8px);
        width: 320px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        z-index: 9998;
        overflow: hidden;
    ">
        {{-- Header --}}
        <div style="display:flex; justify-content:space-between; align-items:center; padding:0.75rem 1rem; border-bottom:1px solid #f3f4f6;">
            <span style="font-weight:600; font-size:0.875rem; color:#111827;">Notifications</span>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('notifications.readAll') }}" style="margin:0;">
                    @csrf
                    <button type="submit" style="background:none;border:none;font-size:0.75rem;color:#4f46e5;cursor:pointer;">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        {{-- List --}}
        <ul style="list-style:none; margin:0; padding:0; max-height:280px; overflow-y:auto;">
            @forelse(auth()->user()->notifications->take(15) as $notif)
                <li style="border-bottom:1px solid #f9fafb; background:{{ is_null($notif->read_at) ? '#eff6ff' : '#fff' }};">
                    <form method="POST" action="{{ route('notifications.read', $notif->id) }}" style="margin:0;">
                        @csrf
                        <button type="submit" style="width:100%;text-align:left;background:none;border:none;padding:0.75rem 1rem;cursor:pointer;display:flex;gap:0.6rem;align-items:flex-start;">
                            <span style="font-size:15px; margin-top:1px;">
                                @php $type = $notif->data['type'] ?? 'info'; @endphp
                                @if($type === 'success') ✅
                                @elseif($type === 'warning') ⚠️
                                @elseif($type === 'alert') 🔔
                                @else ℹ️
                                @endif
                            </span>
                            <div style="flex:1;">
                                <p style="margin:0; font-size:0.8125rem; color:#111827; line-height:1.4;">{{ $notif->data['message'] }}</p>
                                <p style="margin:0.25rem 0 0; font-size:0.75rem; color:#9ca3af;">{{ $notif->created_at->diffForHumans() }}</p>
                            </div>
                            @if(is_null($notif->read_at))
                                <span style="width:8px;height:8px;border-radius:50%;background:#3b82f6;margin-top:4px;flex-shrink:0;"></span>
                            @endif
                        </button>
                    </form>
                </li>
            @empty
                <li style="padding:2rem 1rem; text-align:center; font-size:0.875rem; color:#9ca3af;">
                    No notifications yet.
                </li>
            @endforelse
        </ul>
    </div>
</div>

<script>
function toggleBell() {
    var d = document.getElementById('bell-dropdown');
    d.style.display = d.style.display === 'none' ? 'block' : 'none';
}

document.addEventListener('click', function(e) {
    var wrapper = document.getElementById('bell-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('bell-dropdown').style.display = 'none';
    }
});
</script>
@endauth