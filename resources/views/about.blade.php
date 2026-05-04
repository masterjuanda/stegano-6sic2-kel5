<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #0a0a0f;
            color: #e8e4d9;
            min-height: 100vh;
        }

        /* HERO */
        .hero {
            position: relative;
            padding: 80px 64px 60px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(124, 95, 201, 0.15) 0%, transparent 70%);
        }

        .badge {
            display: inline-block;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #9b7fd4;
            border: 1px solid rgba(155, 127, 212, 0.3);
            padding: 5px 16px;
            border-radius: 100px;
            margin-bottom: 24px;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 56px;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 16px;
            color: #f0ece1;
        }

        .hero h1 span {
            color: #9b7fd4;
        }

        .hero p {
            font-size: 16px;
            font-weight: 300;
            color: rgba(232, 228, 217, 0.55);
            max-width: 480px;
            line-height: 1.8;
        }

        /* STATS */
        .stats-row {
            display: flex;
            padding: 0 64px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        }

        .stat {
            flex: 1;
            padding: 28px 32px;
            border-right: 1px solid rgba(255, 255, 255, 0.07);
        }

        .stat:first-child {
            padding-left: 0;
        }

        .stat:last-child {
            border-right: none;
        }

        .stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            font-weight: 700;
            color: #f0ece1;
            display: block;
        }

        .stat-label {
            font-size: 12px;
            color: rgba(232, 228, 217, 0.4);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        /* MEMBERS */
        .section {
            padding: 56px 64px;
        }

        .section-label {
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(232, 228, 217, 0.35);
            margin-bottom: 36px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.07);
        }

        .members-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .member-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 16px;
            padding: 28px;
            display: flex;
            align-items: flex-start;
            gap: 18px;
            transition: border-color 0.2s, background 0.2s;
        }

        .member-card:hover {
            border-color: rgba(155, 127, 212, 0.3);
            background: rgba(155, 127, 212, 0.05);
        }

        .avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .member-name {
            font-size: 16px;
            font-weight: 500;
            color: #f0ece1;
            margin-bottom: 4px;
        }

        .member-role {
            font-size: 13px;
            color: #9b7fd4;
            margin-bottom: 8px;
        }

        .member-nim {
            font-size: 12px;
            color: rgba(232, 228, 217, 0.3);
            letter-spacing: 0.05em;
        }

        /* FOOTER */
        .page-footer {
            margin: 0 64px;
            padding: 28px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: rgba(232, 228, 217, 0.25);
        }
    </style>
</head>

<body>

    <div class="hero">
        <div class="badge">&#9679; Project Kelompok 5</div>
        <h1>Tentang <span>Kami</span></h1>
        {{-- <p></p> --}}
    </div>

    <div class="stats-row">
        <div class="stat">
            <span class="stat-num">{{ count($members) }}</span>
            <span class="stat-label">Anggota Tim</span>
        </div>
        <div class="stat">
            <span class="stat-num">2023</span>
            <span class="stat-label">Angkatan</span>
        </div>
        <div class="stat">
            <span class="stat-num">Laravel</span>
            <span class="stat-label">Tech Stack</span>
        </div>
    </div>

    <div class="section">
        <div class="section-label">Anggota Kelompok</div>
        <div class="members-grid">
            @foreach ($members as $index => $member)
                @php
                    $colors = [
                        ['bg' => 'rgba(124,95,201,0.2)', 'text' => '#c4a8f8'],
                        ['bg' => 'rgba(30,180,140,0.15)', 'text' => '#5eddb5'],
                        ['bg' => 'rgba(220,95,80,0.15)', 'text' => '#f4a39a'],
                        ['bg' => 'rgba(240,180,50,0.15)', 'text' => '#f5cc6a'],
                        ['bg' => 'rgba(80,140,220,0.15)', 'text' => '#80b8f4'],
                        ['bg' => 'rgba(200,100,160,0.15)', 'text' => '#f0a0d0'],
                    ];
                    $c = $colors[$index % count($colors)];
                    $words = explode(' ', $member['name']);
                    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                @endphp
                <div class="member-card">
                    <div class="avatar" style="background: {{ $c['bg'] }}; color: {{ $c['text'] }};">
                        {{ $initials }}
                    </div>
                    <div>
                        <p class="member-name">{{ $member['name'] }}</p>
                        <p class="member-role">{{ $member['role'] }}</p>
                        <span class="member-nim">NIRM: {{ $member['NIRM'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="page-footer">
        <span>Kelompok 5 &mdash; Kompresi Citra dan Steganografi</span>
        <span>STMIK Triguna Dharma &bull; 2026</span>
    </div>

</body>

</html>
