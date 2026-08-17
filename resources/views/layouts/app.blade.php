<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CAM Festival 2026') — Diocese of Livingstone</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        /* ── Design Tokens ── */
        :root {
            --bg-main: #070b14;
            --bg-card: rgba(14, 21, 38, 0.80);
            --bg-card-hover: rgba(20, 30, 52, 0.92);
            --border-card: rgba(255, 255, 255, 0.07);
            --border-subtle: rgba(255, 255, 255, 0.04);
            --primary: #f59e0b;
            --primary-light: #fbbf24;
            --primary-glow: rgba(245, 158, 11, 0.22);
            --primary-dim: rgba(245, 158, 11, 0.10);
            --accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.18);
            --violet: #8b5cf6;
            --success: #10b981;
            --danger: #ef4444;
            --text-main: #f1f5f9;
            --text-sub: #cbd5e1;
            --text-muted: #64748b;
            --font-display: 'Outfit', system-ui, sans-serif;
            --font-body: 'Plus Jakarta Sans', system-ui, sans-serif;
            --radius-card: 18px;
            --radius-sm: 10px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            background-color: var(--bg-main);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            /* Rich ambient background */
            background-image:
                radial-gradient(ellipse 80% 60% at -10% -5%, rgba(245, 158, 11, 0.10) 0%, transparent 60%),
                radial-gradient(ellipse 60% 60% at 110% -10%, rgba(59, 130, 246, 0.10) 0%, transparent 55%),
                radial-gradient(ellipse 70% 50% at 50% 110%, rgba(139, 92, 246, 0.07) 0%, transparent 60%);
            background-attachment: fixed;
        }

        /* ── HEADER ── */
        header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(7, 11, 20, 0.90);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-card);
        }

        .nav-container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 0 1.5rem;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        /* Brand */
        .brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
            color: var(--text-main);
            flex-shrink: 0;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-weight: 900;
            font-size: 1rem;
            color: #0b0f19;
            box-shadow: 0 0 22px var(--primary-glow), 0 2px 8px rgba(0,0,0,0.4);
            letter-spacing: -0.03em;
        }

        .brand-text h1 {
            font-family: var(--font-display);
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: #fff;
            line-height: 1.15;
        }

        .brand-text p {
            font-size: 0.68rem;
            color: var(--text-muted);
            font-weight: 500;
            letter-spacing: 0.01em;
        }

        /* Nav */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            list-style: none;
            flex-wrap: wrap;
        }

        .nav-item a {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 0.85rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-muted);
            transition: color 0.18s, background 0.18s;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .nav-item a:hover { color: var(--text-main); background: rgba(255,255,255,0.05); }

        .nav-item a.active {
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.10);
            border-color: rgba(245, 158, 11, 0.25);
        }

        /* Divider */
        .nav-divider {
            width: 1px;
            height: 22px;
            background: var(--border-card);
            margin: 0 0.35rem;
            flex-shrink: 0;
        }

        /* Auth nav buttons */
        .btn-portal-judge {
            background: rgba(139, 92, 246, 0.12) !important;
            color: #c4b5fd !important;
            border: 1px solid rgba(139, 92, 246, 0.28) !important;
        }
        .btn-portal-judge:hover, .btn-portal-judge.active {
            background: rgba(139, 92, 246, 0.22) !important;
            color: #fff !important;
        }

        .btn-portal-admin {
            background: rgba(59, 130, 246, 0.12) !important;
            color: #93c5fd !important;
            border: 1px solid rgba(59, 130, 246, 0.28) !important;
        }
        .btn-portal-admin:hover, .btn-portal-admin.active {
            background: rgba(59, 130, 246, 0.22) !important;
            color: #fff !important;
        }

        .btn-portal-login {
            background: rgba(245, 158, 11, 0.12) !important;
            color: #f59e0b !important;
            border: 1px solid rgba(245, 158, 11, 0.32) !important;
            font-weight: 700 !important;
        }
        .btn-portal-login:hover {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            color: #0b0f19 !important;
            box-shadow: 0 0 16px var(--primary-glow);
            transform: translateY(-1px);
        }

        .user-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.38rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid var(--border-card);
            color: var(--text-main);
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.38rem 0.75rem;
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(239, 68, 68, 0.10);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.25);
            cursor: pointer;
            font-family: inherit;
            transition: all 0.18s;
        }
        .btn-logout:hover { background: rgba(239, 68, 68, 0.22); color: #fff; }

        /* Big screen CTA */
        .nav-cta {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            color: #0b0f19 !important;
            font-weight: 800 !important;
            box-shadow: 0 4px 14px var(--primary-glow);
            border: none !important;
        }
        .nav-cta:hover {
            transform: translateY(-1.5px) !important;
            box-shadow: 0 6px 22px var(--primary-glow) !important;
            color: #0b0f19 !important;
        }

        /* ── PAGE HERO BANNER ── */
        .page-hero {
            background: linear-gradient(135deg,
                rgba(245,158,11,0.07) 0%,
                rgba(14,21,38,0.5) 45%,
                rgba(59,130,246,0.04) 100%);
            border-bottom: 1px solid var(--border-subtle);
            padding: 2.5rem 0 2rem;
            margin-bottom: 0;
        }
        .page-hero-inner {
            max-width: 1440px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        .page-hero h2 {
            font-family: var(--font-display);
            font-size: clamp(1.6rem, 3vw, 2.4rem);
            font-weight: 900;
            letter-spacing: -0.03em;
            color: #fff;
            line-height: 1.1;
        }
        .page-hero p { color: var(--text-muted); font-size: 0.9rem; margin-top: 0.35rem; }
        .page-hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #f59e0b;
            margin-bottom: 0.5rem;
        }
        .page-hero-eyebrow::before {
            content: '';
            display: inline-block;
            width: 18px; height: 2px;
            background: #f59e0b;
            border-radius: 2px;
        }

        /* ── MAIN ── */
        main {
            max-width: 1440px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            flex: 1;
        }

        /* ── ALERTS ── */
        .alert {
            padding: 0.9rem 1.1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
        }
        .alert-success { background: rgba(16, 185, 129, 0.10); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; }
        .alert-danger { background: rgba(239, 68, 68, 0.10); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; }

        /* ── GLASS CARD ── */
        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid var(--border-card);
            border-radius: var(--radius-card);
            padding: 1.5rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.25);
        }
        .glass-card-sm { border-radius: 12px; padding: 1.1rem 1.25rem; }

        /* ── STAT CARDS ── */
        .grid-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 1.25rem 1.35rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: border-color 0.2s, transform 0.2s;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            border-radius: 16px 16px 0 0;
            background: linear-gradient(90deg, transparent, var(--stat-color, #f59e0b), transparent);
            opacity: 0.6;
        }
        .stat-card:hover { border-color: rgba(255,255,255,0.12); transform: translateY(-2px); }

        .stat-icon {
            width: 46px; height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-content h4 {
            font-size: 0.68rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .stat-content .stat-val {
            font-family: var(--font-display);
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.15;
            margin-top: 0.1rem;
        }

        .stat-content .stat-sub {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: 0.15rem;
        }

        /* ── BADGES ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.28rem 0.7rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .badge-scheduled { background: rgba(59, 130, 246, 0.12); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.25); }
        .badge-live {
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.35);
            animation: pulse-badge 2s infinite;
        }
        .badge-completed { background: rgba(16, 185, 129, 0.12); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.25); }
        .badge-gold { background: linear-gradient(135deg, #fbbf24, #d97706); color: #0f172a; font-weight: 900; border: none; }
        .badge-silver { background: linear-gradient(135deg, #e2e8f0, #94a3b8); color: #0f172a; font-weight: 900; border: none; }
        .badge-bronze { background: linear-gradient(135deg, #fb923c, #c2410c); color: #fff; font-weight: 900; border: none; }

        @keyframes pulse-badge {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; box-shadow: 0 0 10px rgba(239,68,68,0.4); }
        }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.6rem 1.25rem;
            border-radius: var(--radius-sm);
            font-size: 0.84rem;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
            transition: all 0.18s ease;
            white-space: nowrap;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #0b0f19;
            box-shadow: 0 4px 14px var(--primary-glow);
        }
        .btn-primary:hover { transform: translateY(-1.5px); box-shadow: 0 6px 22px var(--primary-glow); }

        .btn-secondary {
            background: rgba(255,255,255,0.05);
            color: var(--text-sub);
            border-color: var(--border-card);
        }
        .btn-secondary:hover { background: rgba(255,255,255,0.09); color: var(--text-main); }

        .btn-danger { background: rgba(239,68,68,0.15); color: #f87171; border-color: rgba(239,68,68,0.3); }
        .btn-danger:hover { background: rgba(239,68,68,0.25); }

        .btn-sm { padding: 0.38rem 0.8rem; font-size: 0.78rem; border-radius: 8px; }

        /* ── FORM INPUTS ── */
        input[type="text"],
        input[type="number"],
        input[type="time"],
        input[type="date"],
        input[type="email"],
        select, textarea {
            background: rgba(7, 11, 20, 0.8);
            border: 1px solid var(--border-card);
            color: var(--text-main);
            padding: 0.6rem 0.9rem;
            border-radius: 9px;
            font-family: inherit;
            font-size: 0.875rem;
            transition: border-color 0.18s, box-shadow 0.18s;
            -webkit-appearance: none;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: rgba(245, 158, 11, 0.5);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.10);
        }
        select { cursor: pointer; }
        input::placeholder, textarea::placeholder { color: var(--text-muted); }

        /* ── FILTER BAR ── */
        .filter-bar {
            display: flex;
            align-items: flex-end;
            gap: 0.85rem;
            flex-wrap: wrap;
        }
        .filter-group label {
            display: block;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
        }

        /* ── TABLES ── */
        .table-responsive { overflow-x: auto; border-radius: 0 0 var(--radius-card) var(--radius-card); }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }

        thead th {
            background: rgba(7, 11, 20, 0.55);
            color: var(--text-muted);
            font-weight: 700;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-card);
            text-transform: uppercase;
            font-size: 0.68rem;
            letter-spacing: 0.08em;
            white-space: nowrap;
        }

        tbody td {
            padding: 0.95rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.03);
            color: var(--text-main);
        }

        tbody tr:hover td { background: rgba(255,255,255,0.025); }
        tbody tr:last-child td { border-bottom: none; }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 4rem 1.5rem;
            color: var(--text-muted);
        }
        .empty-state .empty-icon { font-size: 3rem; margin-bottom: 1rem; opacity: 0.7; }
        .empty-state h3 { font-size: 1.05rem; font-weight: 700; color: var(--text-sub); margin-bottom: 0.4rem; }
        .empty-state p { font-size: 0.85rem; max-width: 400px; margin: 0 auto; line-height: 1.6; }

        /* ── FOOTER ── */
        footer {
            border-top: 1px solid var(--border-subtle);
            padding: 1.35rem 1.5rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.78rem;
            background: rgba(7, 11, 20, 0.5);
            letter-spacing: 0.01em;
        }

        /* ── LEADERBOARD RANK ── */
        .rank-1 td { background: rgba(245, 158, 11, 0.05) !important; }
        .rank-2 td { background: rgba(148, 163, 184, 0.04) !important; }
        .rank-3 td { background: rgba(251, 146, 60, 0.04) !important; }

        /* ── MICRO-ANIMATIONS ── */
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fade-in-up 0.4s ease both; }
        .animate-in-delay-1 { animation-delay: 0.06s; }
        .animate-in-delay-2 { animation-delay: 0.12s; }
        .animate-in-delay-3 { animation-delay: 0.18s; }
        .animate-in-delay-4 { animation-delay: 0.24s; }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-container { height: auto; padding: 0.75rem 1rem; flex-wrap: wrap; }
            .nav-links { gap: 0.2rem; }
            .nav-item a { padding: 0.4rem 0.65rem; font-size: 0.78rem; }
            main { padding: 1.5rem 1rem; }
            .page-hero { padding: 1.75rem 0 1.5rem; }
        }
    </style>
    @yield('styles')
</head>
<body>

    {{-- ── Header ── --}}
    <header>
        <div class="nav-container">
            <a href="{{ route('program.index') }}" class="brand">
                <div class="brand-logo">CAM</div>
                <div class="brand-text">
                    <h1>CAM Festival 2026</h1>
                    <p>Catholic Diocese of Livingstone</p>
                </div>
            </a>

            <ul class="nav-links">
                <li class="nav-item">
                    <a href="{{ route('program.index') }}" class="{{ request()->routeIs('program.*') ? 'active' : '' }}">
                        📅 Timetable
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('registration.index') }}" class="{{ request()->routeIs('registration.*') ? 'active' : '' }}">
                        ⛪ Parishes
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('leaderboard.index') }}" class="{{ request()->routeIs('leaderboard.index') ? 'active' : '' }}">
                        🏆 Leaderboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('leaderboard.big_screen') }}" class="nav-cta" target="_blank">
                        📺 Big Screen
                    </a>
                </li>

                <li><div class="nav-divider"></div></li>

                @guest
                    <li class="nav-item">
                        <a href="{{ url('/admin/login') }}" class="btn-portal-login {{ request()->is('admin/login*') ? 'active' : '' }}">
                            🔐 Official Login
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ url('/admin/judge-workstation') }}" class="btn-portal-judge {{ request()->is('admin/judge-workstation*') ? 'active' : '' }}">
                            ⚖️ {{ auth()->user()->isJudge() ? auth()->user()->getJudgeName() . ' Workstation' : 'Judge Workstation' }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin') }}" class="btn-portal-admin {{ request()->is('admin') ? 'active' : '' }}">
                            {{ auth()->user()->isAdmin() ? '⚙️ Admin' : '📊 Portal' }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="user-pill" style="border-color: {{ auth()->user()->isAdmin() ? 'rgba(59,130,246,0.35)' : 'rgba(245,158,11,0.35)' }};">
                            {{ auth()->user()->isAdmin() ? '🛡️ ' . auth()->user()->name : '⚖️ ' . auth()->user()->getJudgeName() }}
                        </span>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-logout">🚪 Sign Out</button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </header>

    {{-- ── Page Hero ── --}}
    @hasSection('hero')
    <div class="page-hero">
        <div class="page-hero-inner">
            @yield('hero')
        </div>
    </div>
    @endif

    {{-- ── Main Content ── --}}
    <main>
        @if(session('success'))
            <div class="alert alert-success animate-in">
                <span>✅</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger animate-in">
                <span>⚠️</span>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger animate-in">
                <span>⚠️</span>
                <div>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.2rem;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ── Footer ── --}}
    <footer>
        <p>© 2026 Catholic Association of Youth (CAM) &bull; Catholic Diocese of Livingstone &bull; Festival Management Portal</p>
    </footer>

    @yield('scripts')
</body>
</html>
