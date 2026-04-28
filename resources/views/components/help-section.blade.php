<div style="margin-top:1.25rem;">
    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.6rem;">
        <span style="font-size:1rem;">{{ $icon }}</span>
        <span style="font-size:0.825rem;font-weight:700;color:#1a1a2e;">{{ $title }}</span>
    </div>
    <ul style="margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:0.4rem;">
        @foreach($items as $item)
            <li style="display:flex;gap:0.5rem;font-size:0.78rem;color:#4b5563;line-height:1.5;">
                <span style="color:#a8a4e0;flex-shrink:0;margin-top:2px;">›</span>
                <span>{{ $item }}</span>
            </li>
        @endforeach
    </ul>
</div>