<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — Notification Service</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #0b1120;
            --bg-card: #131c2e;
            --bg-raised: #1a2539;
            --bg-input: #0b1120;
            --border: #26334a;
            --border-strong: #34445f;
            --text: #e8edf5;
            --text-muted: #8b98ad;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --green: #22c55e;
            --green-bg: rgba(34, 197, 94, .12);
            --red: #ef4444;
            --red-bg: rgba(239, 68, 68, .12);
            --amber: #f59e0b;
            --amber-bg: rgba(245, 158, 11, .12);
            --blue-bg: rgba(59, 130, 246, .12);
            --radius: 12px;
            --mono: 'JetBrains Mono', ui-monospace, monospace;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.55;
            font-size: 15px;
            min-height: 100vh;
        }

        a { color: inherit; }

        /* ---------- shell ---------- */
        .shell { display: grid; grid-template-columns: 232px 1fr; min-height: 100vh; }

        .sidebar {
            background: var(--bg-card);
            border-right: 1px solid var(--border);
            padding: 1.25rem .9rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .brand { display: flex; align-items: center; gap: .65rem; padding: 0 .4rem; text-decoration: none; }
        .brand-icon {
            width: 36px; height: 36px; flex: none;
            background: linear-gradient(135deg, #25d366, #128c7e);
            border-radius: 10px;
            display: grid; place-items: center;
        }
        .brand-icon svg { width: 20px; height: 20px; fill: #fff; }
        .brand-name { font-weight: 700; font-size: .95rem; line-height: 1.2; }
        .brand-sub { font-size: .72rem; color: var(--text-muted); }

        .nav { display: flex; flex-direction: column; gap: .2rem; }
        .nav a {
            display: flex; align-items: center; gap: .65rem;
            padding: .55rem .7rem;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            font-size: .9rem;
        }
        .nav a:hover { background: var(--bg-raised); color: var(--text); }
        .nav a[aria-current="page"] { background: var(--blue-bg); color: #bfdbfe; }
        .nav svg { width: 18px; height: 18px; flex: none; }

        .sidebar-foot { margin-top: auto; border-top: 1px solid var(--border); padding-top: 1rem; font-size: .82rem; }
        .user-name { font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .user-mail { color: var(--text-muted); font-size: .75rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: .6rem; }

        .main { padding: 2rem 2.25rem 3rem; min-width: 0; }
        .page-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
        .page-title { font-size: 1.45rem; font-weight: 700; letter-spacing: -.01em; }
        .page-sub { color: var(--text-muted); font-size: .88rem; }

        /* ---------- building blocks ---------- */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.35rem;
            margin-bottom: 1.25rem;
        }
        .card-head { display: flex; justify-content: space-between; align-items: center; gap: .75rem; margin-bottom: 1rem; flex-wrap: wrap; }
        .card-title { font-size: 1rem; font-weight: 600; }
        .card-link { font-size: .82rem; color: #93c5fd; text-decoration: none; }
        .card-link:hover { text-decoration: underline; }

        .grid { display: grid; gap: 1.25rem; }
        .grid-2 { grid-template-columns: 1fr 1fr; }
        .grid-3-1 { grid-template-columns: 2fr 1fr; }

        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.25rem; }
        .stat { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.1rem 1.2rem; }
        .stat-label { font-size: .78rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: .04em; }
        .stat-value { font-size: 1.75rem; font-weight: 700; margin-top: .2rem; font-variant-numeric: tabular-nums; }
        .stat-note { font-size: .8rem; color: var(--text-muted); }
        .stat-note.bad { color: #fca5a5; }
        .stat-note.good { color: #86efac; }

        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
            background: var(--primary);
            color: #fff;
            border: 1px solid transparent;
            border-radius: 8px;
            padding: .6rem 1.1rem;
            font: inherit; font-size: .88rem; font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
        }
        .btn:hover { background: var(--primary-hover); }
        .btn:focus-visible, .nav a:focus-visible, .input:focus-visible { outline: 2px solid #93c5fd; outline-offset: 2px; }
        .btn-ghost { background: transparent; border-color: var(--border-strong); color: var(--text); }
        .btn-ghost:hover { background: var(--bg-raised); }
        .btn-warn { background: var(--amber-bg); border-color: rgba(245, 158, 11, .35); color: #fcd34d; }
        .btn-warn:hover { background: rgba(245, 158, 11, .22); }
        .btn-danger { background: var(--red-bg); border-color: rgba(239, 68, 68, .35); color: #fca5a5; }
        .btn-danger:hover { background: rgba(239, 68, 68, .22); }
        .btn-sm { padding: .35rem .65rem; font-size: .78rem; border-radius: 6px; }
        .btn-block { width: 100%; }

        .chip-btn {
            background: var(--bg-raised); border: 1px solid var(--border-strong); color: var(--text-muted);
            border-radius: 6px; padding: .15rem .5rem; font: inherit; font-size: .72rem; cursor: pointer;
        }
        .chip-btn:hover { color: var(--text); }
        .chip-btn.done { background: var(--green); border-color: var(--green); color: #052e16; }

        .form-row { display: grid; gap: .9rem; align-items: end; }
        .field { display: flex; flex-direction: column; gap: .35rem; min-width: 0; }
        .label { font-size: .78rem; font-weight: 600; color: var(--text-muted); }
        .hint { font-size: .75rem; color: var(--text-muted); }
        .input {
            background: var(--bg-input);
            border: 1px solid var(--border-strong);
            border-radius: 8px;
            color: var(--text);
            padding: .55rem .75rem;
            font: inherit; font-size: .88rem;
            width: 100%;
        }
        .input:focus { border-color: var(--primary); outline: none; }
        select.input { cursor: pointer; }
        textarea.input { resize: vertical; min-height: 4.5rem; }

        .table-wrap { overflow-x: auto; margin: 0 -1.35rem; padding: 0 1.35rem; }
        table { width: 100%; border-collapse: collapse; font-size: .86rem; }
        th {
            text-align: left; font-size: .72rem; font-weight: 600; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: .05em;
            padding: .6rem .75rem; border-bottom: 1px solid var(--border); white-space: nowrap;
        }
        td { padding: .75rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover td { background: rgba(255, 255, 255, .015); }
        .num { text-align: right; font-variant-numeric: tabular-nums; }

        .mono { font-family: var(--mono); font-size: .8rem; }
        .muted { color: var(--text-muted); }
        .small { font-size: .78rem; }
        .nowrap { white-space: nowrap; }
        .row-link { text-decoration: none; }
        .row-link:hover { text-decoration: underline; }

        .badge {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .15rem .55rem; border-radius: 999px;
            font-size: .72rem; font-weight: 600; white-space: nowrap;
        }
        .badge::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .badge-ok { background: var(--green-bg); color: #86efac; }
        .badge-bad { background: var(--red-bg); color: #fca5a5; }
        .badge-off { background: rgba(148, 163, 184, .12); color: #cbd5e1; }
        .tag {
            display: inline-block; font-family: var(--mono); font-size: .72rem;
            background: var(--blue-bg); border: 1px solid rgba(59, 130, 246, .3); color: #bfdbfe;
            padding: .05rem .4rem; border-radius: 4px; margin: .1rem .15rem .1rem 0;
        }

        .key { display: flex; align-items: center; gap: .45rem; }
        .actions { display: flex; gap: .35rem; flex-wrap: wrap; }
        .actions form { display: inline; }

        .alert { border-radius: var(--radius); padding: .9rem 1.1rem; margin-bottom: 1.25rem; font-size: .9rem; border: 1px solid; }
        .alert-ok { background: var(--green-bg); border-color: rgba(34, 197, 94, .35); color: #bbf7d0; }
        .alert-info { background: var(--blue-bg); border-color: rgba(59, 130, 246, .35); color: #bfdbfe; }
        .alert-bad { background: var(--red-bg); border-color: rgba(239, 68, 68, .35); color: #fecaca; }
        .alert-title { font-weight: 600; margin-bottom: .5rem; }
        .alert ul { margin-left: 1.1rem; }
        .secret-grid { display: grid; grid-template-columns: auto 1fr; gap: .4rem 1rem; align-items: center; margin-top: .4rem; }
        .secret-grid .mono { color: #fff; word-break: break-all; }

        .empty { text-align: center; padding: 2.5rem 1rem; color: var(--text-muted); }
        .empty strong { display: block; color: var(--text); margin-bottom: .25rem; }

        pre.code {
            background: var(--bg); border: 1px solid var(--border); border-radius: 8px;
            padding: 1rem; overflow-x: auto; font-family: var(--mono); font-size: .8rem; color: #cbd5e1;
            white-space: pre;
        }

        .pagination { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-top: 1rem; font-size: .84rem; }

        dialog {
            background: var(--bg-card); color: var(--text);
            border: 1px solid var(--border-strong); border-radius: var(--radius);
            padding: 1.5rem; width: min(520px, calc(100vw - 2rem));
            margin: auto;
        }
        dialog::backdrop { background: rgba(2, 6, 23, .7); }
        dialog form > .field { margin-bottom: .9rem; }
        .dialog-actions { display: flex; justify-content: flex-end; gap: .5rem; margin-top: 1.25rem; }

        .bar-list { list-style: none; display: flex; flex-direction: column; gap: .7rem; }
        .bar-list li { font-size: .84rem; }
        .bar-meta { display: flex; justify-content: space-between; gap: .5rem; margin-bottom: .25rem; }
        .bar-track { height: 6px; background: var(--bg-raised); border-radius: 3px; overflow: hidden; }
        .bar-fill { height: 100%; background: var(--primary); border-radius: 3px; }

        .chart { width: 100%; height: auto; display: block; }
        .legend { display: flex; gap: 1rem; font-size: .78rem; color: var(--text-muted); }
        .legend span::before { content: ""; display: inline-block; width: 10px; height: 10px; border-radius: 2px; margin-right: .35rem; vertical-align: -1px; }
        .legend .l-ok::before { background: var(--green); }
        .legend .l-bad::before { background: var(--red); }

        .sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }

        @media (max-width: 1000px) {
            .stats { grid-template-columns: repeat(2, 1fr); }
            .grid-2, .grid-3-1 { grid-template-columns: 1fr; }
        }

        @media (max-width: 760px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { position: static; height: auto; flex-direction: row; flex-wrap: wrap; align-items: center; gap: .75rem; padding: .75rem 1rem; border-right: 0; border-bottom: 1px solid var(--border); }
            .brand-sub, .user-mail, .user-name { display: none; }
            .nav { flex-direction: row; order: 3; width: 100%; overflow-x: auto; }
            .nav a { padding: .45rem .65rem; }
            .sidebar-foot { margin: 0 0 0 auto; border: 0; padding: 0; }
            .main { padding: 1.25rem 1rem 2.5rem; }
            .stats { gap: .75rem; }
            .stat-value { font-size: 1.4rem; }
        }
    </style>
</head>

<body>
    <div class="shell">
        <aside class="sidebar">
            <a href="{{ route('home') }}" class="brand">
                <span class="brand-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.84 9.84 0 0 0 12.04 2z" /></svg>
                </span>
                <span>
                    <span class="brand-name">Notification Service</span><br>
                    <span class="brand-sub">WhatsApp gateway admin</span>
                </span>
            </a>

            <nav class="nav" aria-label="Main">
                <a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 13h6V4H4v9zm0 7h6v-4H4v4zm10 0h6v-9h-6v9zm0-16v4h6V4h-6z" /></svg>
                    Dashboard
                </a>
                <a href="{{ route('services.index') }}" @if (request()->routeIs('services.*')) aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.74 5.74L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.59a1 1 0 01.29-.7l6.97-6.97A6 6 0 1121 9z" /></svg>
                    Services &amp; keys
                </a>
                <a href="{{ route('logs.index') }}" @if (request()->routeIs('logs.*')) aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5m-9 6l2.5-2H19a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v14z" /></svg>
                    Message log
                </a>
            </nav>

            @auth
                <div class="sidebar-foot">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-mail">{{ Auth::user()->email }}</div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm">Log out</button>
                    </form>
                </div>
            @endauth
        </aside>

        <main class="main">
            @if (session('status_message'))
                <div class="alert alert-info" role="status">{{ session('status_message') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-bad" role="alert">
                    <div class="alert-title">Please fix the following</div>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // Values are passed through data-* attributes, never interpolated into inline JS,
        // so names containing quotes or HTML cannot break out of the handler.
        document.addEventListener('click', function (event) {
            const copyBtn = event.target.closest('[data-copy]');
            if (copyBtn) {
                const text = copyBtn.dataset.copy || document.getElementById(copyBtn.dataset.copyFrom)?.innerText || '';
                navigator.clipboard.writeText(text).then(function () {
                    const original = copyBtn.textContent;
                    copyBtn.textContent = 'Copied';
                    copyBtn.classList.add('done');
                    setTimeout(function () {
                        copyBtn.textContent = original;
                        copyBtn.classList.remove('done');
                    }, 1500);
                });
                return;
            }

            const revealBtn = event.target.closest('[data-reveal]');
            if (revealBtn) {
                const target = document.getElementById(revealBtn.dataset.reveal);
                const showing = target.dataset.state === 'shown';
                target.textContent = showing ? target.dataset.masked : target.dataset.full;
                target.dataset.state = showing ? 'masked' : 'shown';
                revealBtn.textContent = showing ? 'Show' : 'Hide';
                return;
            }

            const openBtn = event.target.closest('[data-open-dialog]');
            if (openBtn) {
                document.getElementById(openBtn.dataset.openDialog)?.showModal();
                return;
            }

            const closeBtn = event.target.closest('[data-close-dialog]');
            if (closeBtn) {
                closeBtn.closest('dialog')?.close();
            }
        });

        document.addEventListener('submit', function (event) {
            const message = event.target.dataset.confirm;
            if (message && !window.confirm(message)) {
                event.preventDefault();
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
