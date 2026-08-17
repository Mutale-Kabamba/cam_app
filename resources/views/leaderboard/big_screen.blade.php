<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAM Festival 2026 - Big Screen Leaderboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #070a13;
            color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 1.5rem 2rem;
            background-image: 
                radial-gradient(at 0% 0%, rgba(245, 158, 11, 0.18) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(59, 130, 246, 0.18) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(139, 92, 246, 0.12) 0px, transparent 60%);
            background-attachment: fixed;
        }

        /* Top Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.08);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .title-box {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .logo-badge {
            width: 58px;
            height: 58px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-weight: 900;
            font-size: 1.8rem;
            color: #070a13;
            box-shadow: 0 0 30px rgba(245, 158, 11, 0.4);
        }

        .title-text h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            background: linear-gradient(to right, #fff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .title-text p {
            font-size: 1rem;
            color: #94a3b8;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .live-tag {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 2px solid rgba(239, 68, 68, 0.5);
            padding: 0.4rem 1.1rem;
            border-radius: 9999px;
            font-weight: 800;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.03); opacity: 0.85; }
        }

        /* Category Filter Navigation Bar */
        .category-nav-bar {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem;
            scrollbar-width: thin;
        }

        .cat-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1rem;
            border-radius: 10px;
            background: rgba(23, 31, 50, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.88rem;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .cat-pill:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .cat-pill.active {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #070a13;
            border-color: #f59e0b;
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.35);
        }

        /* Standings Grid */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(410px, 1fr));
            gap: 1.15rem;
            flex: 1;
        }

        .rank-card {
            background: rgba(23, 31, 50, 0.8);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.15rem 1.4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
            transition: all 0.25s ease;
        }

        .rank-card.first {
            border: 2px solid #fbbf24;
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.16), rgba(23, 31, 50, 0.9));
            box-shadow: 0 0 35px rgba(251, 191, 36, 0.25);
        }

        .rank-card.second {
            border: 1.5px solid rgba(226, 232, 240, 0.6);
            background: linear-gradient(135deg, rgba(226, 232, 240, 0.08), rgba(23, 31, 50, 0.85));
        }

        .rank-card.third {
            border: 1.5px solid rgba(249, 115, 22, 0.6);
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.08), rgba(23, 31, 50, 0.85));
        }

        .rank-num {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 900;
            width: 52px;
            color: #94a3b8;
            display: flex;
            align-items: center;
        }

        .rank-card.first .rank-num { color: #fbbf24; }
        .rank-card.second .rank-num { color: #cbd5e1; }
        .rank-card.third .rank-num { color: #fdba74; }

        .parish-info {
            flex: 1;
            padding-left: 0.5rem;
            padding-right: 0.75rem;
        }

        .parish-name {
            font-family: 'Outfit', sans-serif;
            font-size: 1.28rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: 0.2rem;
        }

        .parish-meta {
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .points-box {
            text-align: right;
            min-width: 90px;
        }

        .points-val {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 900;
            color: #f59e0b;
            line-height: 1;
        }

        .points-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            margin-top: 0.2rem;
        }
    </style>
    <script>
        // Auto refresh every 30 seconds for live big screen projection
        setTimeout(function() {
            window.location.reload();
        }, 30000);
    </script>
</head>
<body>
    <div class="header">
        <div class="title-box">
            <div class="logo-badge">CAM</div>
            <div class="title-text">
                @if($activeCategory)
                    <h1>{{ strtoupper($activeCategory->name) }} LIVE STANDINGS</h1>
                    <p>Catholic Diocese of Livingstone &bull; Max: {{ $activeCategory->max_raw_score }} Marks &bull; {{ $activeCategory->allocated_minutes > 0 ? $activeCategory->allocated_minutes . ' mins on stage' : 'Quiz Competition' }}</p>
                @else
                    <h1>CAM FESTIVAL 2026 OVERALL CHAMPIONSHIP</h1>
                    <p>Catholic Diocese of Livingstone Official Cumulative Leaderboard</p>
                @endif
            </div>
        </div>
        <div class="header-actions">
            <div class="live-tag">
                <span>●</span> LIVE UPDATES
            </div>
        </div>
    </div>

    <!-- Category Filter Bar on Big Screen -->
    <div class="category-nav-bar">
        <a href="{{ route('leaderboard.big_screen') }}" class="cat-pill {{ !$selectedCategory ? 'active' : '' }}">
            🏆 Overall Championship
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('leaderboard.big_screen', ['category_id' => $cat->id]) }}" class="cat-pill {{ $selectedCategory == $cat->id ? 'active' : '' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>

    <!-- Standings Cards Grid -->
    <div class="grid-container">
        @if($activeCategory && $categoryResults)
            @forelse($categoryResults as $idx => $res)
                <div class="rank-card {{ $idx === 0 ? 'first' : ($idx === 1 ? 'second' : ($idx === 2 ? 'third' : '')) }}">
                    <div class="rank-num">
                        @if($idx === 0) 🥇 @elseif($idx === 1) 🥈 @elseif($idx === 2) 🥉 @else #{{ $idx + 1 }} @endif
                    </div>
                    <div class="parish-info">
                        <div class="parish-name">{{ $res['parish']->name }}</div>
                        <div class="parish-meta">{{ $res['parish']->deanery ?? 'Livingstone' }} &bull; Code: {{ $res['parish']->code }}</div>
                    </div>
                    <div class="points-box">
                        <div class="points-val">{{ $res['final_score'] }}</div>
                        <div class="points-label">Score / {{ $activeCategory->max_raw_score }}</div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem; color: #94a3b8; font-size: 1.25rem;">
                    ⏳ Official scores for {{ $activeCategory->name }} are pending adjudication.
                </div>
            @endforelse
        @else
            @forelse($standings as $idx => $st)
                <div class="rank-card {{ $idx === 0 ? 'first' : ($idx === 1 ? 'second' : ($idx === 2 ? 'third' : '')) }}">
                    <div class="rank-num">
                        @if($idx === 0) 🥇 @elseif($idx === 1) 🥈 @elseif($idx === 2) 🥉 @else #{{ $idx + 1 }} @endif
                    </div>
                    <div class="parish-info">
                        <div class="parish-name">{{ $st['parish']->name }}</div>
                        <div class="parish-meta">{{ $st['parish']->deanery ?? 'Livingstone' }} &bull; {{ $st['parish']->code }}</div>
                    </div>
                    <div class="points-box">
                        <div class="points-val">{{ $st['total_points'] }}</div>
                        <div class="points-label">Championship Pts</div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem; color: #94a3b8; font-size: 1.25rem;">
                    🏆 No parish championship standings recorded yet.
                </div>
            @endforelse
        @endif
    </div>
</body>
</html>
