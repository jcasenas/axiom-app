{{-- Help & Documentation Panel --}}
<div id="help-wrapper" style="position:relative;">

    {{-- Trigger Button --}}
    <button onclick="toggleHelp()" class="nav-icon-btn" aria-label="Help & Documentation"
        style="color:#a8a4e0;">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3
                     0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994
                     1.093m0 3h.01M12 21a9 9 0 100-18 9 9 0 000 18z"/>
        </svg>
    </button>

    {{-- Slide-out Panel --}}
    <div id="help-panel" style="
        display: none;
        position: fixed;
        top: 64px;
        right: 0;
        width: 360px;
        height: calc(100vh - 64px);
        background: #fff;
        border-left: 1px solid #e5e7eb;
        box-shadow: -4px 0 24px rgba(0,0,0,0.1);
        z-index: 999;
        overflow-y: auto;
        flex-direction: column;
    ">
        {{-- Header --}}
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff;z-index:1;">
            <div style="display:flex;align-items:center;gap:0.6rem;">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#a8a4e0" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3
                             0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994
                             1.093m0 3h.01M12 21a9 9 0 100-18 9 9 0 000 18z"/>
                </svg>
                <span style="font-size:0.95rem;font-weight:700;color:#1a1a2e;">Help & Documentation</span>
            </div>
            <button onclick="toggleHelp()" style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:1.2rem;line-height:1;">✕</button>
        </div>

        {{-- Role Badge --}}
        <div style="padding:1rem 1.5rem 0;">
            <span style="
                display:inline-block;
                background:#ede9fe;
                color:#5b21b6;
                font-size:0.7rem;
                font-weight:700;
                padding:3px 10px;
                border-radius:999px;
                letter-spacing:0.05em;
                text-transform:uppercase;
            ">{{ Auth::user()->role->role_name }} Guide</span>
        </div>

        {{-- Content --}}
        <div style="padding:1rem 1.5rem 2rem;">

            @php $role = Auth::user()->role->role_name; @endphp

            {{-- ── ADMIN ── --}}
            @if($role === 'Admin')

                @include('components.help-section', [
                    'icon'  => '👥',
                    'title' => 'Managing Users',
                    'items' => [
                        'Go to Manage Users to view all students, faculty, and librarians.',
                        'Use the Approve button to activate a pending registration.',
                        'Use the Reject button to decline a registration — the user will be notified.',
                        'Use Deactivate to suspend an active account.',
                        'You can also manually create users using the Add User button.',
                    ]
                ])

                @include('components.help-section', [
                    'icon'  => '📚',
                    'title' => 'Managing Books',
                    'items' => [
                        'Go to Manage Books to view the full e-book catalog.',
                        'Use Add Book to add a new title with author, category, format, and copies.',
                        'Edit a book to update available copies, status, or file URL.',
                        'Archiving a book hides it from students but preserves borrow history.',
                    ]
                ])

                @include('components.help-section', [
                    'icon'  => '📋',
                    'title' => 'Borrow Records',
                    'items' => [
                        'View all borrow activity across all users.',
                        'Filter by department, status, or date range.',
                        'Export records to PDF using the Export button.',
                    ]
                ])

                @include('components.help-section', [
                    'icon'  => '⚙️',
                    'title' => 'System Settings',
                    'items' => [
                        'Set the borrow window (how many days a borrow lasts).',
                        'Set max borrow limits per role.',
                        'Toggle system maintenance mode.',
                    ]
                ])

                @include('components.help-section', [
                    'icon'  => '🔔',
                    'title' => 'Notifications',
                    'items' => [
                        'You receive a bell notification when a new user registers.',
                        'Click a notification to go directly to the relevant page.',
                        'Use Mark All as Read to clear the badge count.',
                    ]
                ])

            {{-- ── LIBRARIAN ── --}}
            @elseif($role === 'Librarian')

                @include('components.help-section', [
                    'icon'  => '✅',
                    'title' => 'Approving Borrow Requests',
                    'items' => [
                        'Go to Borrow Records to see all pending requests.',
                        'Click Approve to grant access — the student is notified automatically.',
                        'Click Reject to decline — the student is notified with a reason.',
                        'Approved borrows have an access window set by the Admin in System Settings.',
                    ]
                ])

                @include('components.help-section', [
                    'icon'  => '📚',
                    'title' => 'Managing Books',
                    'items' => [
                        'You can update available copies and book status.',
                        'You cannot add or archive books — contact the Admin for that.',
                    ]
                ])

                @include('components.help-section', [
                    'icon'  => '⏰',
                    'title' => 'Overdue & Expiring',
                    'items' => [
                        'The system automatically marks borrowings as expired at midnight.',
                        'Available copies are restored automatically when a borrow expires.',
                        'You receive a bell notification summarising how many expired each day.',
                        'Due Soon means the borrow expires within 2 days.',
                    ]
                ])

                @include('components.help-section', [
                    'icon'  => '🔔',
                    'title' => 'Notifications',
                    'items' => [
                        'You are notified when a student or faculty submits a borrow request.',
                        'You are notified when a student cancels a pending request.',
                        'You receive a daily summary of expired borrowings.',
                    ]
                ])

            {{-- ── STUDENT / FACULTY ── --}}
            @else

                @include('components.help-section', [
                    'icon'  => '🔍',
                    'title' => 'Browsing Books',
                    'items' => [
                        'Go to Browse Books to see all available e-books.',
                        'Filter by category or search by title, author, or ISBN.',
                        'Books showing 0 available copies cannot be borrowed at this time.',
                    ]
                ])

                @include('components.help-section', [
                    'icon'  => '📖',
                    'title' => 'Borrowing a Book',
                    'items' => [
                        'Click the Borrow button on any available book.',
                        'Your request goes to Pending until a librarian approves it.',
                        'You will receive a bell notification once approved or rejected.',
                        $role === 'Faculty'
                            ? 'As Faculty, you can have up to 5 active borrows at a time.'
                            : 'As a Student, you can have up to 3 active borrows at a time.',
                    ]
                ])

                @include('components.help-section', [
                    'icon'  => '📋',
                    'title' => 'My Books',
                    'items' => [
                        'Current Borrows shows your pending, active, and due soon borrows.',
                        'Borrow History shows your expired and cancelled records.',
                        'You can cancel a pending request before a librarian acts on it.',
                        'Due Soon means your access expires within 2 days — plan accordingly.',
                    ]
                ])

                @include('components.help-section', [
                    'icon'  => '🔔',
                    'title' => 'Notifications',
                    'items' => [
                        'You are notified when your account is approved or rejected.',
                        'You are notified when a borrow request is approved or rejected.',
                        'You receive a reminder when your access is expiring in 1–2 days.',
                        'You are notified when your access expires.',
                    ]
                ])

            @endif

            {{-- Common to all roles --}}
            @include('components.help-section', [
                'icon'  => '👤',
                'title' => 'Your Profile',
                'items' => [
                    'Update your profile photo from My Profile.',
                    'Change your password anytime from My Profile.',
                ]
            ])

            {{-- Contact --}}
            <div style="margin-top:1.5rem;padding:1rem;background:#f5f3ff;border-radius:0.75rem;border:1px solid #ede9fe;">
                <p style="margin:0 0 0.4rem;font-size:0.8rem;font-weight:700;color:#5b21b6;">Need more help?</p>
                <p style="margin:0;font-size:0.78rem;color:#6b7280;line-height:1.5;">
                    Contact your system administrator or library staff for assistance with account or access issues.
                </p>
            </div>

        </div>
    </div>
</div>

<script>
function toggleHelp() {
    var panel = document.getElementById('help-panel');
    panel.style.display = panel.style.display === 'flex' ? 'none' : 'flex';
}

document.addEventListener('click', function(e) {
    var wrapper = document.getElementById('help-wrapper');
    var panel   = document.getElementById('help-panel');
    if (wrapper && !wrapper.contains(e.target) && panel.style.display === 'flex') {
        panel.style.display = 'none';
    }
});
</script>