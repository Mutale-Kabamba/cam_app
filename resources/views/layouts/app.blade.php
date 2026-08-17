<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CAM Festival 2026') - Diocese of Livingstone</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-main: #0b0f19;
            --bg-card: rgba(23, 31, 50, 0.75);
            --bg-card-hover: rgba(30, 41, 67, 0.9);
            --border-card: rgba(255, 255, 255, 0.08);
            --primary: #f59e0b;
            --primary-glow: rgba(245, 158, 11, 0.25);
            --accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.2);
            --success: #10b981;
            --danger: #ef4444;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --font-display: 'Outfit', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--bg-main);
            color: var(--text-main);
            min-height: 100vh;
            background-image: 
                radial-gradient(at 0% 0%, rgba(245, 158, 11, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(59, 130, 246, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(139, 92, 246, 0.08) 0px, transparent 60%);
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
        }

        /* Glassmorphism Header */
        header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(11, 15, 25, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-card);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-main);
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 1.2rem;
            color: #0b0f19;
            box-shadow: 0 0 20px var(--primary-glow);
        }

        .brand-text h1 {
            font-family: var(--font-display);
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            background: linear-gradient(to right, #fff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-text p {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            list-style: none;
            flex-wrap: wrap;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 0.85rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .nav-item a:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-item a.active {
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.12);
            border-color: rgba(245, 158, 11, 0.3);
        }

        .btn-portal-judge {
            background: rgba(139, 92, 246, 0.15) !important;
            color: #c4b5fd !important;
            border: 1px solid rgba(139, 92, 246, 0.35) !important;
        }
        .btn-portal-judge:hover, .btn-portal-judge.active {
            background: rgba(139, 92, 246, 0.28) !important;
            color: #fff !important;
            box-shadow: 0 0 15px rgba(139, 92, 246, 0.3);
        }

        .btn-portal-admin {
            background: rgba(59, 130, 246, 0.15) !important;
            color: #93c5fd !important;
            border: 1px solid rgba(59, 130, 246, 0.35) !important;
        }
        .btn-portal-admin:hover, .btn-portal-admin.active {
            background: rgba(59, 130, 246, 0.28) !important;
            color: #fff !important;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
        }

        .btn-portal-login {
            background: rgba(245, 158, 11, 0.15) !important;
            color: #f59e0b !important;
            border: 1px solid rgba(245, 158, 11, 0.4) !important;
            font-weight: 700 !important;
            border-radius: 8px;
            padding: 0.5rem 0.95rem !important;
            transition: all 0.2s ease;
        }
        .btn-portal-login:hover, .btn-portal-login.active {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            color: #0b0f19 !important;
            box-shadow: 0 0 15px var(--primary-glow);
            transform: translateY(-1px);
        }

        .user-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid var(--border-card);
            color: var(--text-main);
        }

        .btn-logout {
            background: rgba(239, 68, 68, 0.12) !important;
            color: #f87171 !important;
            border: 1px solid rgba(239, 68, 68, 0.3) !important;
            padding: 0.4rem 0.8rem !important;
            border-radius: 8px;
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-family: inherit;
        }
        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.25) !important;
            color: #fff !important;
            border-color: rgba(239, 68, 68, 0.5) !important;
        }

        .nav-cta {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #0b0f19 !important;
            border-radius: 8px;
            padding: 0.5rem 0.95rem !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 14px var(--primary-glow);
        }

        .nav-cta:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px var(--primary-glow);
        }

        /* Main Container */
        main {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            flex: 1;
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .alert-success { background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; }
        .alert-danger { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171; }

        /* Glass Cards */
        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.24);
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .badge-scheduled { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
        .badge-live { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4); animation: pulse-red 2s infinite; }
        .badge-completed { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .badge-gold { background: linear-gradient(135deg, #fbbf24, #d97706); color: #0f172a; font-weight: 800; }
        .badge-silver { background: linear-gradient(135deg, #e2e8f0, #94a3b8); color: #0f172a; font-weight: 800; }
        .badge-bronze { background: linear-gradient(135deg, #fdba74, #ea580c); color: #0f172a; font-weight: 800; }

        @keyframes pulse-red {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.03); }
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #0b0f19;
            box-shadow: 0 4px 14px var(--primary-glow);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px var(--primary-glow);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-main);
            border-color: var(--border-card);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.4);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.3);
        }

        .btn-sm {
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
            border-radius: 6px;
        }

        /* Form Inputs */
        input[type="text"], input[type="number"], input[type="time"], input[type="date"], select, textarea {
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid var(--border-card);
            color: #fff;
            padding: 0.6rem 0.9rem;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.9rem;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 10px rgba(245, 158, 11, 0.2);
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.9rem;
        }

        th {
            background: rgba(15, 23, 42, 0.65);
            color: var(--text-muted);
            font-weight: 600;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-card);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: var(--text-main);
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Footer */
        footer {
            border-top: 1px solid var(--border-card);
            padding: 1.5rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            background: rgba(11, 15, 25, 0.6);
        }

        /* Stats Grid */
        .grid-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 14px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            background: rgba(255, 255, 255, 0.05);
        }

        .stat-content h4 {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-content .stat-val {
            font-family: var(--font-display);
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-main);
        }
    </style>
    @yield('styles')
</head>
<body>
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
                <!-- Public Navigation (Read-Only & Results) -->
                <li class="nav-item">
                    <a href="{{ route('program.index') }}" class="{{ request()->routeIs('program.*') && !request()->is('admin/*') ? 'active' : '' }}">
                        📅 Timetable
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('registration.index') }}" class="{{ request()->routeIs('registration.*') && !request()->is('admin/*') ? 'active' : '' }}">
                        ⛪ Parishes
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('leaderboard.index') }}" class="{{ request()->routeIs('leaderboard.index') ? 'active' : '' }}">
                        🏆 Leaderboard & Results
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('leaderboard.big_screen') }}" class="nav-cta" target="_blank">
                        📺 Big Screen
                    </a>
                </li>

                <!-- Authentication & Dedicated Portals -->
                @guest
                    <li class="nav-item" style="margin-left: 0.5rem; border-left: 1px solid var(--border-card); padding-left: 0.75rem;">
                        <a href="{{ url('/admin/login') }}" class="btn-portal-login {{ request()->is('admin/login*') || request()->is('login*') ? 'active' : '' }}">
                            🔐 Official Login
                        </a>
                    </li>
                @else
                    <li class="nav-item" style="margin-left: 0.5rem; border-left: 1px solid var(--border-card); padding-left: 0.75rem;">
                        <a href="{{ url('/admin/judge-workstation') }}" class="btn-portal-judge {{ request()->is('admin/judge-workstation*') ? 'active' : '' }}">
                            ⚖️ {{ auth()->user()->isJudge() ? auth()->user()->getJudgeName() . ' Workstation' : 'Judge Workstation' }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/admin') }}" class="btn-portal-admin {{ request()->is('admin') ? 'active' : '' }}">
                            {{ auth()->user()->isAdmin() ? '⚙️ Admin Dashboard' : '📊 Portal Dashboard' }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="user-pill" style="border-color: {{ auth()->user()->isAdmin() ? 'rgba(59, 130, 246, 0.4)' : 'rgba(245, 158, 11, 0.4)' }};">
                            {{ auth()->user()->isAdmin() ? '🛡️ ' . auth()->user()->name : '⚖️ ' . auth()->user()->getJudgeName() }}
                        </span>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                            @csrf
                            <button type="submit" class="btn-logout" title="Sign out">
                                🚪 Sign Out
                            </button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </header>

    <main>
        @if(session('success'))
            <div class="alert alert-success">
                <span>✅</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <span>⚠️</span>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <span>⚠️</span>
                <div>
                    <ul style="list-style: none; margin: 0;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer>
        <p>&copy; 2026 Catholic Association of Youth (CAM) &bull; Catholic Diocese of Livingstone Festival Management Portal</p>
    </footer>

    @yield('scripts')
</body>
</html>
