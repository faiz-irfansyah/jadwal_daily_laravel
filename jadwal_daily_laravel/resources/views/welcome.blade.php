<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f7f8f4">
    <title>Ruang Hari — {{ config('app.name', 'Jadwal Pribadi') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{--ink:#1e302c;--muted:#78847d;--green:#3c705d;--green-dark:#285744;--mint:#e7f0e9;--paper:#f7f8f4;--white:#fff;--line:#e8ebe5;--orange:#e89b61;--shadow:0 14px 40px #263b3010}
        *{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:var(--paper);color:var(--ink);font-family:'DM Sans',sans-serif;-webkit-font-smoothing:antialiased}a{color:inherit;text-decoration:none}button{font:inherit}.shell{max-width:1120px;margin:auto;padding:0 34px}.topbar{height:78px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--line)}.brand{display:flex;gap:11px;align-items:center;font-family:Manrope,sans-serif;font-weight:800;font-size:17px;letter-spacing:-.5px}.brand-mark{width:36px;height:36px;border-radius:12px;background:var(--green);color:#fff;display:grid;place-items:center;font-size:18px}.brand small{display:block;font:500 10px 'DM Sans',sans-serif;color:var(--muted);letter-spacing:.3px;margin-top:1px}.nav{display:flex;gap:30px;align-items:center;color:#737f77;font-size:13px;font-weight:600}.nav a.active{color:var(--green)}.avatar{width:36px;height:36px;border-radius:50%;background:#e8dfd1;color:#755b3c;display:grid;place-items:center;font-size:12px;font-weight:700}.page{padding:42px 0 70px}.welcome{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px}.eyebrow{font-size:11px;letter-spacing:1.35px;text-transform:uppercase;font-weight:700;color:var(--green);margin:0 0 10px}.welcome h1{font:700 clamp(26px,3vw,36px)/1.16 Manrope,sans-serif;letter-spacing:-1.1px;margin:0}.welcome p.sub{color:var(--muted);font-size:14px;margin:10px 0 0}.date-pill{background:#fff;border:1px solid var(--line);border-radius:13px;padding:12px 16px;display:flex;gap:10px;align-items:center;font-size:13px;font-weight:600;box-shadow:0 4px 12px #263b3008}.date-pill span{color:var(--green);font-size:16px}.date-pill input{border:0;background:transparent;color:inherit;font:inherit;width:120px;outline:0}.overview{display:grid;grid-template-columns:1.65fr .85fr;gap:18px;margin-bottom:24px}.hero-card{position:relative;overflow:hidden;border-radius:22px;background:var(--green);color:white;padding:25px 28px;min-height:176px;display:flex;justify-content:space-between;align-items:center}.hero-card:after{content:"";position:absolute;width:230px;height:230px;right:-45px;top:-112px;border-radius:50%;border:1px solid #ffffff20;box-shadow:0 0 0 27px #ffffff08,0 0 0 55px #ffffff06}.hero-copy{position:relative;z-index:1;max-width:390px}.hero-label{font-size:11px;text-transform:uppercase;letter-spacing:1.1px;color:#c4dbcc;font-weight:700}.hero-card h2{font:700 22px/1.28 Manrope,sans-serif;letter-spacing:-.5px;margin:11px 0 7px}.hero-card p{color:#d8e7dc;font-size:12px;line-height:1.6;margin:0}.hero-icon{position:relative;z-index:1;width:62px;height:62px;border:1px solid #ffffff36;border-radius:20px;display:grid;place-items:center;font-size:27px;margin-right:18px}.stat-card{background:#fff;border:1px solid var(--line);border-radius:22px;padding:23px 25px;display:flex;flex-direction:column;justify-content:space-between;box-shadow:var(--shadow)}.stat-head{color:var(--muted);font-size:12px;font-weight:600;display:flex;justify-content:space-between}.stat-number{font:800 31px Manrope,sans-serif;letter-spacing:-1.2px;margin:11px 0 1px}.stat-note{font-size:11px;color:var(--muted)}.progress{height:6px;border-radius:20px;background:#edf0eb;margin-top:15px;overflow:hidden}.progress span{display:block;background:#75a487;height:100%;border-radius:20px}.section-head{display:flex;justify-content:space-between;align-items:center;margin:28px 0 15px}.section-head h2{font:700 18px Manrope,sans-serif;letter-spacing:-.45px;margin:0}.section-head a{font-size:12px;color:var(--green);font-weight:700}.timeline{background:#fff;border:1px solid var(--line);border-radius:20px;padding:8px 22px;box-shadow:var(--shadow)}.activity{display:grid;grid-template-columns:78px 19px minmax(0,1fr) auto;gap:12px;align-items:center;min-height:76px;position:relative}.activity+.activity{border-top:1px solid #f0f1ee}.time{font-size:12px;font-weight:700;color:#62716a;white-space:nowrap}.track{height:100%;position:relative;display:grid;place-items:center}.track:before{content:"";position:absolute;top:0;bottom:0;width:1px;background:#e4eae4}.activity:first-child .track:before{top:50%}.activity:last-child .track:before{bottom:50%}.dot{position:relative;z-index:1;width:9px;height:9px;background:#fff;border:2px solid #9ab7a1;border-radius:50%}.activity.current .dot{background:var(--green);border-color:var(--green);box-shadow:0 0 0 4px #e4efe6}.activity.done .dot{background:#a9c5b0;border-color:#a9c5b0}.activity-title{font-size:13px;font-weight:700;color:#35463f}.activity-sub{font-size:11px;color:#99a29b;margin-top:4px}.tag{font-size:10px;padding:6px 9px;border-radius:8px;background:#f0f4ef;color:#708176;font-weight:700;white-space:nowrap}.tag.locked{background:#f4f1e9;color:#958251}.tag.now{background:#e7f1e9;color:var(--green)}.empty{padding:38px 22px;text-align:center;color:var(--muted)}.empty-icon{font-size:30px;margin-bottom:10px}.empty strong{display:block;color:var(--ink);font-size:14px;margin-bottom:6px}.empty p{font-size:12px;margin:0 0 16px}.empty code{background:#eef2ec;color:var(--green-dark);padding:7px 10px;border-radius:8px;font-size:11px}.bottom-nav{display:none}.footer{text-align:center;color:#a0a9a2;font-size:11px;padding:27px 0 0}
        .checkin-button{min-height:36px;border:1px solid #dce7dd;border-radius:9px;background:#fff;color:#52735f;font-size:10px;font-weight:700;padding:7px 10px;cursor:pointer;white-space:nowrap}.checkin-button.checked{background:#e7f1e9;color:#32644a;border-color:#d9e9dc}.checkin-button.skip{color:#8a7162;border-color:#eee4db}.checkin-button:disabled{opacity:.45;cursor:not-allowed}@media(min-width:761px){.activity{grid-template-columns:78px 19px minmax(0,1fr) auto auto}}
        .ai-planner{margin:22px 0 26px;padding:21px 23px;background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:var(--shadow)}.ai-planner-head{display:flex;align-items:center;gap:12px;margin-bottom:13px}.ai-planner-icon{width:38px;height:38px;flex:none;display:grid;place-items:center;border-radius:13px;background:var(--mint);color:var(--green);font-size:19px}.ai-planner h2{font:700 16px Manrope,sans-serif;margin:0 0 3px}.ai-planner p{font-size:11px;line-height:1.55;color:var(--muted);margin:0}.ai-planner form{display:flex;gap:10px;align-items:stretch}.ai-planner textarea{flex:1;min-width:0;min-height:76px;padding:12px 13px;border:1px solid #e2e8e1;border-radius:12px;resize:vertical;color:var(--ink);font:13px/1.5 'DM Sans',sans-serif;outline-color:#8eb29a}.ai-planner button{align-self:flex-end;min-height:44px;border:0;border-radius:11px;background:var(--green);color:#fff;padding:0 17px;font-size:12px;font-weight:700;cursor:pointer}.ai-planner button:hover{background:var(--green-dark)}.ai-error{margin:12px 0 0;padding:10px 12px;border-radius:10px;background:#fff2eb;color:#945c3e;font-size:12px}.ai-hint{margin-top:9px!important;font-size:10px!important}
        .day-reminder-panel{margin:20px 0;padding:18px 21px;border:1px solid #e6ecdf;border-radius:18px;background:#f3f7ef}.day-reminder-head{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:9px}.day-reminder-head h2{font:700 15px Manrope,sans-serif;margin:0}.day-reminder-head a{font-size:11px;font-weight:700;color:var(--green)}.day-reminder{display:grid;grid-template-columns:70px minmax(0,1fr);gap:10px;padding:11px 0;border-top:1px solid #e4ebdf}.day-reminder time{font-size:11px;font-weight:700;color:var(--green)}.day-reminder strong{font-size:12px}.day-reminder p{margin:4px 0 0;color:var(--muted);font-size:11px;line-height:1.45;white-space:pre-wrap}
        @media(max-width:760px){.day-reminder-panel{padding:16px;margin:17px 0;border-radius:16px}.day-reminder-head h2{font-size:14px}.day-reminder{grid-template-columns:55px minmax(0,1fr);gap:8px}.day-reminder time{font-size:10px}}
        @media(max-width:760px){.shell{padding:0 21px}.topbar{height:66px}.nav{display:none}.page{padding:29px 0 calc(94px + env(safe-area-inset-bottom))}.welcome{display:block;margin-bottom:22px}.welcome h1{font-size:28px}.welcome p.sub{font-size:12px;margin-top:7px}.date-pill{width:max-content;margin-top:17px;padding:10px 12px}.overview{grid-template-columns:1fr;gap:11px;margin-bottom:23px}.hero-card{min-height:158px;padding:21px 21px;border-radius:19px}.hero-card h2{font-size:20px;max-width:260px}.hero-card p{font-size:11px;max-width:260px}.hero-icon{width:49px;height:49px;border-radius:16px;font-size:22px;margin:0 4px 0 8px}.stat-card{min-height:116px;border-radius:18px;padding:17px 19px;display:grid;grid-template-columns:1fr auto;align-items:center}.stat-head{grid-column:1/-1}.stat-number{font-size:26px;margin:3px 0}.stat-note{justify-self:end}.progress{grid-column:1/-1;margin-top:4px}.section-head{margin:25px 2px 13px}.section-head h2{font-size:17px}.timeline{padding:5px 14px;border-radius:18px}.activity{grid-template-columns:60px 15px minmax(0,1fr);gap:9px;min-height:73px}.time{font-size:10px}.activity-title{font-size:12px;line-height:1.35}.activity-sub{font-size:10px}.tag{grid-column:3;justify-self:start;margin-top:-11px;margin-bottom:9px;padding:4px 7px}.activity{padding:10px 0}.activity:has(.tag){min-height:82px}.activity .track{grid-row:1/-1;grid-column:2}.activity .time{grid-row:1/-1;align-self:start;margin-top:4px}.activity .activity-title{grid-column:3}.activity .activity-sub{grid-column:3}.activity .tag{grid-column:3}.activity form{grid-column:3;justify-self:start;margin-top:-7px}.checkin-button{min-height:42px}.bottom-nav{position:fixed;z-index:5;bottom:0;left:0;right:0;display:flex;justify-content:space-around;background:#ffffffed;backdrop-filter:blur(16px);border-top:1px solid #e7eae5;padding:10px 16px calc(10px + env(safe-area-inset-bottom))}.bottom-nav a{display:flex;flex-direction:column;align-items:center;gap:4px;color:#87928a;font-size:9px;font-weight:700;min-width:48px}.bottom-nav a span{font-size:17px}.bottom-nav a.active{color:var(--green)}.footer{padding-top:20px;font-size:10px}}
        @media(max-width:760px){.ai-planner{padding:17px 16px;margin:18px 0 22px;border-radius:18px}.ai-planner form{display:grid;grid-template-columns:1fr}.ai-planner textarea{min-height:90px;font-size:14px}.ai-planner button{width:100%;min-height:48px}.ai-planner-head{align-items:flex-start}}
        @media(max-width:360px){.shell{padding:0 15px}.hero-card{padding:18px}.hero-card h2{font-size:18px}.activity{grid-template-columns:54px 13px minmax(0,1fr);gap:7px}.tag{font-size:9px}}
        @media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}}
    </style>
</head>
<body>
    @php
        $selectedDate = $selectedDate ?? now(config('scheduler.timezone'))->toDateString();
        $dateObject = \Illuminate\Support\Carbon::parse($selectedDate, config('scheduler.timezone'));
        $activities = $schedule?->activities ?? [];
        usort($activities, fn ($a, $b) => $a['start'] <=> $b['start']);
        $nowLocal = now(config('scheduler.timezone'));
        $isViewingToday = $selectedDate === $nowLocal->toDateString();
        $minuteNow = $nowLocal->hour * 60 + $nowLocal->minute;
        $activityStart = fn ($item) => (int) substr($item['start'], 0, 2) * 60 + (int) substr($item['start'], 3, 2);
        $activityEnd = fn ($item) => (int) substr($item['end'], 0, 2) * 60 + (int) substr($item['end'], 3, 2);
        $currentIndex = $isViewingToday ? collect($activities)->search(fn ($item) => $activityEnd($item) <= $activityStart($item) ? ($minuteNow >= $activityStart($item) || $minuteNow < $activityEnd($item)) : ($activityStart($item) <= $minuteNow && $activityEnd($item) > $minuteNow)) : false;
        $completeCount = $checkins->where('status', 'done')->count();
        $progressPercent = count($activities) ? (int) round($completeCount / count($activities) * 100) : 0;
        $dateLabel = $dateObject->locale('id')->translatedFormat('l, d F Y');
    @endphp
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="/"><span class="brand-mark">✳</span><span>Ruang Hari<small>PERSONAL SCHEDULER</small></span></a>
            <nav class="nav" aria-label="Navigasi utama"><a class="active" href="{{ route('dashboard') }}">Hari ini</a><a href="{{ route('agenda') }}">Agenda</a><a href="{{ route('progress') }}">Progres</a><a href="{{ route('routines') }}">Rutinitas</a><a href="{{ route('reminders') }}">Pengingat</a><a href="{{ route('settings') }}">Pengaturan</a></nav>
            <div class="avatar" aria-label="Profil pengguna">RH</div>
        </header>
        <main class="page">
            <section class="welcome" id="ringkasan">
                <div><p class="eyebrow">{{ $dateObject->isToday() ? 'HARI INI, ' : '' }}JADWAL PERSONAL</p><h1>Ritme harimu.</h1><p class="sub">Satu langkah pada satu waktu. Kamu sudah melakukannya dengan baik.</p></div>
                <form class="date-pill" method="get" action="/" aria-label="Pilih tanggal"><span>▦</span><input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" aria-label="Pilih tanggal jadwal"></form>
            </section>
            <section class="overview" aria-label="Ringkasan hari">
                <div class="hero-card"><div class="hero-copy"><span class="hero-label">{{ $currentIndex !== false ? 'SEDANG BERLANGSUNG' : 'JALANI HARI DENGAN TENANG' }}</span><h2>{{ $currentIndex !== false ? $activities[$currentIndex]['title'] : 'Ruang untuk hal yang penting.' }}</h2><p>{{ $currentIndex !== false ? $activities[$currentIndex]['start'].' – '.$activities[$currentIndex]['end'].' · Tetap fokus, kamu pasti bisa.' : 'Jadwalmu sudah disiapkan. Lihat agenda hari ini dan mulai dari yang paling dekat.' }}</p></div><div class="hero-icon">{{ $currentIndex !== false ? '◷' : '☀' }}</div></div>
                <div class="stat-card"><div class="stat-head"><span>Check-in kegiatan</span><span>✦</span></div><div class="stat-number">{{ $progressPercent }}<span style="font-size:17px">%</span></div><span class="stat-note">{{ $completeCount }} dilakukan · {{ $checkins->where('status', 'skipped')->count() }} dilewati dari {{ count($activities) }}</span><div class="progress"><span style="width:{{ $progressPercent }}%"></span></div></div>
            </section>
            @if($dayReminders->isNotEmpty())
                <section class="day-reminder-panel" aria-label="Pengingat pada tanggal ini">
                    <div class="day-reminder-head"><h2>🔔 Pengingat tanggal ini</h2><a href="{{ route('reminders') }}">Kelola pengingat</a></div>
                    @foreach($dayReminders as $dayReminder)
                        <article class="day-reminder"><time>{{ substr($dayReminder->reminder_time, 0, 5) }}</time><div><strong>{{ $dayReminder->title }}</strong>@if($dayReminder->notes)<p>{{ $dayReminder->notes }}</p>@endif</div></article>
                    @endforeach
                </section>
            @endif
            @if($isViewingToday)
                <section class="ai-planner" aria-labelledby="ai-planner-title">
                    <div class="ai-planner-head"><span class="ai-planner-icon" aria-hidden="true">✦</span><div><h2 id="ai-planner-title">Atur jadwal dengan AI</h2><p>Tulis perubahan seperti sedang bercerita. AI akan menambahkan atau menata kegiatan hari ini.</p></div></div>
                    <form method="post" action="{{ route('schedule.ai-adjust') }}">
                        @csrf
                        <textarea name="instruction" maxlength="2000" required aria-label="Instruksi perubahan jadwal" placeholder="Contoh: today after my work shift i will hang with my friend for 1 hours">{{ old('instruction') }}</textarea>
                        <button type="submit">✦ Sesuaikan jadwal</button>
                    </form>
                    <p class="ai-hint">Work Shift 14:00–23:00 tetap terkunci. AI hanya menyimpan perubahan yang tidak membuat jadwal bertabrakan.</p>
                    @if(session('ai_error'))<p class="ai-error" role="alert">{{ session('ai_error') }}</p>@endif
                    @error('instruction')<p class="ai-error" role="alert">{{ $message }}</p>@enderror
                </section>
            @endif
            <section id="jadwal">
                <div class="section-head"><h2>Agenda harian</h2><a href="?date={{ now(config('scheduler.timezone'))->toDateString() }}">{{ $dateLabel }}⌄</a></div>
                @if(session('status'))<p class="activity-sub" role="status" style="padding:0 4px 10px;color:var(--green)">{{ session('status') }}</p>@endif
                @if(session('checkin_error'))<p class="activity-sub" role="alert" style="padding:0 4px 10px;color:#a05249">{{ session('checkin_error') }}</p>@endif
                <div class="timeline">
                    @forelse($activities as $index => $activity)
                        @php
                            $startMinute = $activityStart($activity);
                            $endMinute = $activityEnd($activity);
                            $isCurrent = $isViewingToday && ($endMinute <= $startMinute ? ($minuteNow >= $startMinute || $minuteNow < $endMinute) : ($startMinute <= $minuteNow && $endMinute > $minuteNow));
                            $isDone = $isViewingToday && ($endMinute <= $startMinute ? ($minuteNow >= $endMinute && $minuteNow < $startMinute) : $endMinute <= $minuteNow);
                            $checkinStatus = $checkins->get((string) $activity['id'])?->status ?? 'pending';
                            $checkinAvailable = \App\Support\ScheduleCheckinWindow::canCheckIn($selectedDate, $activity['start'], $nowLocal);
                            $checkinOpensAt = \App\Support\ScheduleCheckinWindow::opensAt($selectedDate, $activity['start']);
                            $futureSchedule = $selectedDate > $nowLocal->toDateString();
                            $disableDone = $futureSchedule || ($checkinStatus !== 'done' && ! $checkinAvailable);
                            $disableSkip = $futureSchedule || ($checkinStatus !== 'skipped' && ! $checkinAvailable);
                        @endphp
                        <article class="activity {{ $isCurrent ? 'current' : '' }} {{ $isDone ? 'done' : '' }}">
                            <time class="time">{{ $activity['start'] }} – {{ $activity['end'] }}</time><span class="track"><i class="dot"></i></span>
                            <div><div class="activity-title">{{ $activity['title'] }}</div><div class="activity-sub">{{ $checkinStatus === 'done' ? 'Sudah dilakukan' : ($checkinStatus === 'skipped' ? 'Ditandai dilewati' : (! $checkinAvailable && ! $futureSchedule ? 'Check-in tersedia pukul '.$checkinOpensAt->format('H:i') : ($isCurrent ? 'Sedang berlangsung' : (($activity['locked'] ?? false) ? 'Waktu tetap · tidak dapat digeser' : 'Belum check-in')))) }}</div></div>
                            <form method="post" action="{{ route('checkins.store') }}">
                                @csrf<input type="hidden" name="schedule_date" value="{{ $selectedDate }}"><input type="hidden" name="activity_id" value="{{ $activity['id'] }}"><input type="hidden" name="return_to" value="dashboard"><input type="hidden" name="status" value="{{ $checkinStatus === 'done' ? 'pending' : 'done' }}">
                                <button class="checkin-button {{ $checkinStatus === 'done' ? 'checked' : '' }}" type="submit" @if($disableDone) disabled title="Check-in tersedia mulai 5 menit sebelum kegiatan" @endif>{{ $checkinStatus === 'done' ? '✓ Dilakukan · batal' : '＋ Dilakukan' }}</button>
                            </form>
                            @if($checkinStatus !== 'skipped')<form method="post" action="{{ route('checkins.store') }}">
                                @csrf<input type="hidden" name="schedule_date" value="{{ $selectedDate }}"><input type="hidden" name="activity_id" value="{{ $activity['id'] }}"><input type="hidden" name="return_to" value="dashboard"><input type="hidden" name="status" value="skipped">
                                <button class="checkin-button skip" type="submit" @if($disableSkip) disabled title="Check-in tersedia mulai 5 menit sebelum kegiatan" @endif>Tidak dilakukan</button>
                            </form>@else<form method="post" action="{{ route('checkins.store') }}">
                                @csrf<input type="hidden" name="schedule_date" value="{{ $selectedDate }}"><input type="hidden" name="activity_id" value="{{ $activity['id'] }}"><input type="hidden" name="return_to" value="dashboard"><input type="hidden" name="status" value="pending">
                                <button class="checkin-button skip" type="submit">Batal lewati</button>
                            </form>@endif
                        </article>
                    @empty
                        <div class="empty"><div class="empty-icon">🌿</div><strong>Jadwalmu sedang menunggu.</strong><p>Jalankan generator harian untuk menyiapkan agenda sesuai rutinitasmu.</p><code>php artisan schedule:generate</code></div>
                    @endforelse
                </div>
            </section>
            <section id="fokus" class="footer">Disusun dengan tenang, dijalani dengan seimbang · Waktu Bali (WITA)</section>
        </main>
    </div>
    <nav class="bottom-nav" aria-label="Navigasi bawah"><a class="active" href="{{ route('dashboard') }}"><span>⌂</span>Hari ini</a><a href="{{ route('agenda') }}"><span>▦</span>Agenda</a><a href="{{ route('progress') }}"><span>✧</span>Progres</a><a href="{{ route('routines') }}"><span>↻</span>Rutinitas</a><a href="{{ route('reminders') }}"><span>🔔</span>Pengingat</a><a href="{{ route('settings') }}"><span>⚙</span>Setelan</a></nav>
</body>
</html>
