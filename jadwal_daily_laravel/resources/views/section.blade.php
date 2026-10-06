<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f7f8f4"><title>{{ ucfirst($page) }} — Ruang Hari</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{--ink:#1e302c;--muted:#78847d;--green:#3c705d;--dark:#285744;--mint:#e7f0e9;--paper:#f7f8f4;--white:#fff;--line:#e8ebe5;--shadow:0 14px 40px #263b3010}*{box-sizing:border-box}body{margin:0;background:var(--paper);color:var(--ink);font-family:'DM Sans',sans-serif;-webkit-font-smoothing:antialiased}a{color:inherit;text-decoration:none}button,input,textarea{font:inherit}.shell{max-width:1120px;margin:auto;padding:0 34px}.topbar{height:78px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--line)}.brand{display:flex;gap:11px;align-items:center;font:800 17px Manrope,sans-serif;letter-spacing:-.5px}.mark{width:36px;height:36px;border-radius:12px;background:var(--green);color:#fff;display:grid;place-items:center;font-size:18px}.brand small{display:block;font:500 10px 'DM Sans',sans-serif;color:var(--muted);letter-spacing:.3px;margin-top:1px}.nav{display:flex;gap:25px;align-items:center;color:#737f77;font-size:13px;font-weight:600}.nav a.active{color:var(--green)}.avatar{width:36px;height:36px;border-radius:50%;background:#e8dfd1;color:#755b3c;display:grid;place-items:center;font-size:12px;font-weight:700}.page{padding:38px 0 92px}.eyebrow{font-size:11px;letter-spacing:1.3px;text-transform:uppercase;font-weight:700;color:var(--green);margin:0 0 9px}.heading{display:flex;justify-content:space-between;align-items:end;gap:18px;margin-bottom:25px}.heading h1{font:700 clamp(25px,3vw,34px)/1.2 Manrope,sans-serif;letter-spacing:-1px;margin:0}.heading p{font-size:13px;color:var(--muted);margin:8px 0 0}.card{background:white;border:1px solid var(--line);border-radius:18px;padding:20px 22px;box-shadow:var(--shadow)}.week-controls{display:flex;align-items:center;gap:10px}.week-controls a,.button{min-height:42px;display:inline-flex;justify-content:center;align-items:center;padding:0 14px;border-radius:11px;border:1px solid var(--line);background:white;color:var(--green);font-weight:700;font-size:12px}.button.primary{background:var(--green);color:white;border-color:var(--green)}.days{display:grid;gap:12px}.day{display:grid;grid-template-columns:155px minmax(0,1fr);gap:20px;align-items:start}.day-date{font-weight:700;font-size:13px}.day-date small{display:block;color:var(--muted);font-weight:500;font-size:11px;margin-top:5px}.day-items{display:grid;gap:8px}.mini-item{display:grid;grid-template-columns:105px minmax(0,1fr) auto;gap:12px;align-items:center;font-size:12px;padding:9px 11px;background:#f8faf7;border-radius:10px}.mini-item time{font-weight:700;color:#67766c;white-space:nowrap}.mini-item strong{font-size:12px}.locked{font-size:9px;color:#958251;background:#f4f1e9;border-radius:7px;padding:5px 7px;white-space:nowrap}.empty{padding:22px;text-align:center;color:var(--muted);font-size:12px;background:#fafbf9;border-radius:12px}.stack{display:grid;gap:14px}.form-label{display:block;font-size:12px;font-weight:700;margin:0 0 8px}.field{width:100%;border:1px solid #dfe5dd;border-radius:12px;background:#fff;padding:13px 14px;color:var(--ink);outline:none}.field:focus{border-color:#74a188;box-shadow:0 0 0 3px #e7f0e9}.textarea{min-height:112px;resize:vertical}.form-row{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:11px}.hint{font-size:11px;color:var(--muted)}.status{padding:12px 14px;border-radius:11px;background:#e8f3e9;color:#315b45;font-size:12px;font-weight:600}.error{font-size:11px;color:#aa4c40;margin-top:6px}.entry{display:flex;justify-content:space-between;gap:16px;padding:14px 0;border-bottom:1px solid #eef0ec}.entry:last-child{border-bottom:0}.entry p{font-size:13px;line-height:1.55;margin:0;white-space:pre-wrap}.entry time{font-size:10px;color:var(--muted);white-space:nowrap}.recommendation{border-left:3px solid #81a98a;background:#f2f7f1;padding:16px;border-radius:0 11px 11px 0;font-size:13px;line-height:1.6;white-space:pre-wrap}.service{display:flex;justify-content:space-between;align-items:center;padding:14px 0;border-bottom:1px solid #eef0ec;font-size:13px}.service:last-child{border-bottom:0}.badge{border-radius:99px;padding:6px 10px;font-size:10px;font-weight:700}.badge.ok{background:#e8f3e9;color:#427157}.badge.missing{background:#f7eee4;color:#976a35}.template{padding:13px 0;border-bottom:1px solid #eef0ec}.template:last-child{border-bottom:0}.template strong{font-size:13px}.template small{display:block;color:var(--muted);font-size:11px;margin-top:5px}.footer{text-align:center;color:#a0a9a2;font-size:10px;padding-top:24px}.bottom{display:none}
        @media(max-width:760px){.shell{padding:0 18px}.topbar{height:66px}.nav{display:none}.page{padding:27px 0 calc(92px + env(safe-area-inset-bottom))}.heading{align-items:start;flex-direction:column;margin-bottom:19px}.heading h1{font-size:27px}.heading p{font-size:12px}.card{padding:16px;border-radius:16px}.week-controls{width:100%;justify-content:space-between}.week-controls a{flex:1}.days{gap:10px}.day{display:block}.day-date{margin-bottom:9px}.day-date small{display:inline;margin-left:7px}.mini-item{grid-template-columns:75px minmax(0,1fr);gap:8px;padding:10px}.mini-item time{font-size:10px}.mini-item strong{font-size:11px;line-height:1.4}.locked{grid-column:2;justify-self:start;margin-top:-3px}.form-row{align-items:start;flex-direction:column}.form-row .button{width:100%;min-height:46px}.entry{display:block}.entry time{display:block;margin-top:7px}.bottom{position:fixed;z-index:5;left:0;right:0;bottom:0;display:flex;justify-content:space-around;background:#ffffffed;backdrop-filter:blur(16px);border-top:1px solid #e7eae5;padding:10px 14px calc(10px + env(safe-area-inset-bottom))}.bottom a{display:flex;flex-direction:column;align-items:center;gap:4px;color:#87928a;font-size:9px;font-weight:700;min-width:54px}.bottom a span{font-size:17px}.bottom a.active{color:var(--green)}}
        .checkin-button{min-height:36px;border:1px solid #dce7dd;border-radius:9px;background:#fff;color:#52735f;font-size:10px;font-weight:700;padding:7px 10px;cursor:pointer;white-space:nowrap}.checkin-button.checked{background:#e7f1e9;color:#32644a;border-color:#d9e9dc}.checkin-button:disabled{opacity:.45;cursor:not-allowed}.template-tabs{display:flex;gap:9px;overflow:auto;margin-bottom:15px;padding:2px 1px 7px}.template-tab{flex:0 0 auto;min-width:135px;padding:11px 13px;border:1px solid var(--line);border-radius:12px;background:#fff;font-size:11px;font-weight:700}.template-tab small{display:block;color:var(--muted);font-size:9px;font-weight:500;margin-top:5px}.template-tab.active{border-color:#a9c7b0;background:#edf4ed;color:var(--green-dark)}.routine-row{display:flex;gap:10px;align-items:center;padding:14px 0;border-bottom:1px solid #eef0ec}.routine-row:last-child{border-bottom:0}.routine-edit,.routine-add{display:grid;grid-template-columns:minmax(150px,1fr) 112px 112px auto;align-items:end;gap:9px;flex:1}.routine-edit label,.routine-add label{display:grid;gap:6px}.routine-edit label span,.routine-add label span{font-size:10px;font-weight:700;color:var(--muted)}.routine-edit .field,.routine-add .field{padding:10px;border-radius:9px;font-size:12px;min-width:0}.routine-edit .button,.routine-add .button{min-height:40px}.routine-add{grid-template-columns:minmax(150px,1fr) 130px 130px auto}.delete-button{min-height:40px;padding:0 10px;border:1px solid #f0dddd;background:#fff;color:#a05249;border-radius:9px;font-size:10px;font-weight:700;cursor:pointer}
        @media(max-width:760px){.routine-row{align-items:stretch;flex-direction:column;gap:6px}.routine-edit{grid-template-columns:1fr 1fr;gap:9px}.routine-edit label:first-child{grid-column:1/-1}.routine-edit .button{grid-column:1/-1;min-height:44px}.routine-edit .locked{grid-column:1/-1;justify-self:start;margin:0}.routine-row>form:last-child{align-self:flex-end}.routine-add{grid-template-columns:1fr 1fr;gap:9px}.routine-add label:first-of-type{grid-column:1/-1}.routine-add .button{grid-column:1/-1;min-height:46px}.template-tab{min-width:120px}.bottom a{min-width:43px}.mini-item{grid-template-columns:76px minmax(0,1fr) auto auto}.mini-checkin{grid-column:2/-1;justify-self:start}.mini-checkin button{min-height:38px}}
        .reminder-form{display:grid;grid-template-columns:minmax(0,1.4fr) minmax(145px,.8fr) minmax(130px,.7fr);gap:12px;align-items:end}.reminder-form label{display:grid;gap:7px}.reminder-form label>span{font-size:11px;font-weight:700;color:var(--muted)}.reminder-form .field{min-width:0}.reminder-form .reminder-notes{grid-column:1/-1}.reminder-form .textarea{min-height:76px}.reminder-form>.button,.reminder-actions{grid-column:1/-1}.reminder-actions .button{min-height:42px}.reminder-row{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:12px;padding:17px 0;border-bottom:1px solid #eef0ec;align-items:end}.reminder-row:last-child{border-bottom:0}.reminder-row>form:last-child{padding-bottom:1px}.agenda-reminder{margin-top:9px;padding:8px 9px;border-radius:9px;background:#f1f6ef;color:#456d54;font-size:10px;line-height:1.45}.agenda-reminder strong,.agenda-reminder span{display:block}.agenda-reminder span{margin-top:3px;color:#77857a;white-space:pre-wrap;font-weight:400}
        @media(max-width:760px){.reminder-form{grid-template-columns:1fr 1fr;gap:10px}.reminder-form label:first-of-type,.reminder-form .reminder-notes,.reminder-form>.button,.reminder-actions{grid-column:1/-1}.reminder-form .field{min-height:45px}.reminder-form .textarea{min-height:90px}.reminder-row{grid-template-columns:1fr;gap:8px}.reminder-row>form:last-child{justify-self:end}.agenda-reminder{font-size:11px}.bottom{gap:2px;padding-left:6px;padding-right:6px}.bottom a{min-width:0;flex:1;font-size:8px}}
    </style>
</head>
<body>
    @php $active = $page; @endphp
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="{{ route('dashboard') }}"><span class="mark">✳</span><span>Ruang Hari<small>PERSONAL SCHEDULER</small></span></a>
            <nav class="nav" aria-label="Navigasi utama">
                <a class="{{ $active === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">Hari ini</a>
                <a class="{{ $active === 'agenda' ? 'active' : '' }}" href="{{ route('agenda') }}">Agenda</a>
                <a class="{{ $active === 'progress' ? 'active' : '' }}" href="{{ route('progress') }}">Progres</a>
                <a class="{{ $active === 'routines' ? 'active' : '' }}" href="{{ route('routines') }}">Rutinitas</a>
                <a class="{{ $active === 'reminders' ? 'active' : '' }}" href="{{ route('reminders') }}">Pengingat</a>
                <a class="{{ $active === 'reminders' ? 'active' : '' }}" href="{{ route('reminders') }}">Pengingat</a>
                <a class="{{ $active === 'settings' ? 'active' : '' }}" href="{{ route('settings') }}">Pengaturan</a>
            </nav>
            <div class="avatar" title="Ruang Hari">RH</div>
        </header>

        <main class="page">
            @if($page === 'agenda')
                <div class="heading"><div><p class="eyebrow">PANTAU RUTINITAS</p><h1>Agenda mingguan.</h1><p>{{ $weekStart->locale('id')->translatedFormat('d F') }} – {{ $weekEnd->locale('id')->translatedFormat('d F Y') }}</p></div>
                    <div class="week-controls"><a href="{{ route('agenda', ['week' => $previousWeek]) }}" aria-label="Minggu sebelumnya">← Sebelumnya</a><a href="{{ route('agenda') }}">Minggu ini</a><a href="{{ route('agenda', ['week' => $nextWeek]) }}" aria-label="Minggu berikutnya">Berikutnya →</a></div>
                </div>
                @if(session('checkin_error'))<p class="status" role="alert" style="margin-bottom:14px;background:#fff2eb;color:#945c3e">{{ session('checkin_error') }}</p>@endif
                <section class="days">
                    @foreach($days as $day)
                        <article class="card day">
                            <div class="day-date">{{ $day['date']->locale('id')->translatedFormat('l, d F') }}<small>{{ count($day['activities']) }} kegiatan · {{ $day['checkins']->where('status', 'done')->count() }} selesai</small>
                                @foreach($day['reminders'] as $dayReminder)<div class="agenda-reminder"><strong>🔔 {{ $dayReminder->title }}</strong><span>{{ substr($dayReminder->reminder_time, 0, 5) }} · {{ $dayReminder->notes }}</span></div>@endforeach
                            </div>
                            @if(count($day['activities']))
                                <div class="day-items">
                                    @foreach($day['activities'] as $activity)
                                        @php($checkinStatus = $day['checkins']->get((string) ($activity['id'] ?? ''))?->status ?? 'pending')
                                        @php($checkinAvailable = \App\Support\ScheduleCheckinWindow::canCheckIn($day['date']->toDateString(), $activity['start']))
                                        <div class="mini-item"><time>{{ $activity['start'] }}–{{ $activity['end'] }}</time><a href="{{ route('dashboard', ['date' => $day['date']->toDateString()]) }}"><strong>{{ $activity['title'] }}</strong></a>
                                            @if($activity['locked'] ?? false)<span class="locked">WAKTU TETAP</span>@endif
                                            <form class="mini-checkin" method="post" action="{{ route('checkins.store') }}">@csrf<input type="hidden" name="schedule_date" value="{{ $day['date']->toDateString() }}"><input type="hidden" name="activity_id" value="{{ $activity['id'] }}"><input type="hidden" name="return_to" value="agenda"><input type="hidden" name="status" value="{{ $checkinStatus === 'done' ? 'pending' : 'done' }}"><button class="checkin-button {{ $checkinStatus === 'done' ? 'checked' : '' }}" type="submit" @if(! $checkinAvailable && $checkinStatus !== 'done') disabled title="Check-in tersedia mulai 5 menit sebelum kegiatan" @endif>{{ $checkinStatus === 'done' ? '✓' : 'Check-in' }}</button></form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty">Jadwal belum dibuat untuk tanggal ini.</div>
                            @endif
                        </article>
                    @endforeach
                </section>
            @elseif($page === 'progress')
                <div class="heading"><div><p class="eyebrow">BELAJAR & KONSISTENSI</p><h1>Catatan progres.</h1><p>Minggu mulai {{ $weekStart->locale('id')->translatedFormat('d F Y') }}. Catatan ini dipakai Gemini untuk memberi fokus belajar mingguan.</p></div></div>
                <div class="stack">
                    @if(session('status'))<div class="status" role="status">{{ session('status') }}</div>@endif
                    <section class="card">
                        <form method="post" action="{{ route('progress.store') }}">
                            @csrf<label class="form-label" for="entry">Apa progres belajarmu hari ini?</label>
                            <textarea class="field textarea" id="entry" name="entry" maxlength="500" required placeholder="Contoh: menyelesaikan latihan regresi linear selama 45 menit…">{{ old('entry') }}</textarea>
                            @error('entry')<div class="error">{{ $message }}</div>@enderror
                            <div class="form-row"><span class="hint">Maksimal 500 karakter · disimpan dengan waktu Bali</span><button class="button primary" type="submit">Simpan progres</button></div>
                        </form>
                    </section>
                    @if($recommendations)<section class="card"><p class="eyebrow">SARAN GEMINI TERAKHIR</p><div class="recommendation">{{ $recommendations }}</div></section>@endif
                    <section class="card"><p class="eyebrow">RIWAYAT MINGGU INI · {{ $entries->count() }} CATATAN</p>
                        @forelse($entries as $entry)<article class="entry"><p>{{ $entry['text'] ?? '' }}</p><time>{{ isset($entry['logged_at']) ? \Illuminate\Support\Carbon::parse($entry['logged_at'])->locale('id')->translatedFormat('D, d M · H:i') : '' }}</time></article>
                        @empty<div class="empty">Belum ada progres tercatat. Tambahkan catatan pertama di atas.</div>@endforelse
                    </section>
                </div>
            @elseif($page === 'routines')
                <div class="heading"><div><p class="eyebrow">KEGIATAN BERULANG</p><h1>Template rutinitas.</h1><p>Template diterapkan otomatis sesuai hari. Perubahan dibantu AI untuk merapikan jadwal hari ini dan besok.</p></div></div>
                @if(session('status'))<div class="status" role="status" style="margin-bottom:14px">{{ session('status') }}</div>@endif
                <div class="template-tabs" aria-label="Pilih template">
                    @foreach($templates as $template)<a class="template-tab {{ $selectedTemplate?->id === $template->id ? 'active' : '' }}" href="{{ route('routines', ['template' => $template->id]) }}">{{ $template->name }}<small>{{ collect($template->days_of_week)->map(fn ($day) => ucfirst($day))->join(', ') }}</small></a>@endforeach
                </div>
                @if($selectedTemplate)
                    <div class="stack">
                        <section class="card"><p class="eyebrow">KEGIATAN TEMPLATE · {{ $selectedTemplate->name }}</p>
                            @foreach($selectedTemplate->activities as $activity)
                                <article class="routine-row">
                                    <form class="routine-edit" method="post" action="{{ route('routines.activities.update', [$selectedTemplate, $activity['id'] ?? 'missing']) }}">
                                        @csrf @method('PATCH')
                                        <label><span>Nama kegiatan</span><input class="field" name="title" maxlength="120" value="{{ $activity['title'] }}" required {{ ($activity['locked'] ?? false) ? 'readonly' : '' }}></label>
                                        <label><span>Mulai</span><input class="field" type="time" name="start" value="{{ $activity['start'] }}" required {{ ($activity['locked'] ?? false) ? 'readonly' : '' }}></label>
                                        <label><span>Selesai</span><input class="field" type="time" name="end" value="{{ $activity['end'] }}" required {{ ($activity['locked'] ?? false) ? 'readonly' : '' }}></label>
                                        @if($activity['locked'] ?? false)<span class="locked">🔒 TETAP</span>@else<button class="button primary" type="submit">Simpan</button>@endif
                                    </form>
                                    @if(!($activity['locked'] ?? false))<form method="post" action="{{ route('routines.activities.destroy', [$selectedTemplate, $activity['id'] ?? 'missing']) }}" onsubmit="return confirm('Hapus kegiatan ini dari template?')">@csrf @method('DELETE')<button class="delete-button" type="submit" aria-label="Hapus {{ $activity['title'] }}">Hapus</button></form>@endif
                                </article>
                            @endforeach
                            @error('activity')<p class="error">{{ $message }}</p>@enderror
                        </section>
                        <section class="card"><p class="eyebrow">TAMBAH KEGIATAN</p>
                            <form class="routine-add" method="post" action="{{ route('routines.activities.store', $selectedTemplate) }}">
                                @csrf<label><span>Nama kegiatan</span><input class="field" name="title" maxlength="120" value="{{ old('title') }}" placeholder="Contoh: Jalan pagi" required></label>
                                <label><span>Mulai</span><input class="field" type="time" name="start" value="{{ old('start', '10:00') }}" required></label>
                                <label><span>Selesai</span><input class="field" type="time" name="end" value="{{ old('end', '11:00') }}" required></label>
                                <button class="button primary" type="submit">＋ Tambah</button>
                            </form>
                            @foreach(['title','start','end'] as $field) @error($field)<p class="error">{{ $message }}</p>@enderror @endforeach
                            <p class="hint" style="margin:12px 0 0">Work Shift 14:00–23:00 terkunci. AI akan mencoba menyelaraskan kegiatan, lalu aplikasi memeriksa bentrok sebelum menyimpan.</p>
                        </section>
                    </div>
                @else
                    <section class="card empty">Template belum ada. Jalankan <code>php artisan schedule:seed-templates</code> sekali.</section>
                @endif
            @elseif($page === 'reminders')
                <div class="heading"><div><p class="eyebrow">CATATAN BERDASARKAN TANGGAL</p><h1>Pengingat hari.</h1><p>Simpan kejadian penting dan tentukan kapan kami mengingatkanmu lewat Telegram pada tanggal tersebut.</p></div></div>
                <div class="stack">
                    @if(session('status'))<div class="status" role="status">{{ session('status') }}</div>@endif
                    @if($errors->any())<div class="status" role="alert" style="background:#fff2eb;color:#945c3e">Periksa kembali tanggal, waktu, dan isi pengingat.</div>@endif
                    <section class="card reminder-create">
                        <p class="eyebrow">TAMBAH PENGINGAT</p>
                        <form method="post" action="{{ route('reminders.store') }}" class="reminder-form">
                            @csrf
                            <label><span>Nama kejadian</span><input class="field" name="title" maxlength="120" value="{{ old('title') }}" placeholder="Contoh: Janji dengan dokter" required>@error('title')<small class="error">{{ $message }}</small>@enderror</label>
                            <label><span>Tanggal</span><input class="field" type="date" name="reminder_date" min="{{ $today }}" value="{{ old('reminder_date', $today) }}" required>@error('reminder_date')<small class="error">{{ $message }}</small>@enderror</label>
                            <label><span>Ingatkan pukul</span><input class="field" type="time" name="reminder_time" value="{{ old('reminder_time', '09:00') }}" required>@error('reminder_time')<small class="error">{{ $message }}</small>@enderror</label>
                            <label class="reminder-notes"><span>Catatan</span><textarea class="field textarea" name="notes" maxlength="2000" placeholder="Detail yang perlu diingat…">{{ old('notes') }}</textarea>@error('notes')<small class="error">{{ $message }}</small>@enderror</label>
                            <button class="button primary" type="submit">＋ Simpan pengingat</button>
                        </form>
                        <p class="hint" style="margin:12px 0 0">Telegram mengirim satu pengingat pada tanggal itu, mulai dari waktu yang dipilih. Pastikan bot dan Chat ID sudah dikonfigurasi.</p>
                    </section>
                    <section class="card"><p class="eyebrow">DAFTAR PENGINGAT · {{ $reminders->count() }}</p>
                        @forelse($reminders as $reminder)
                            <article class="reminder-row">
                                <form method="post" action="{{ route('reminders.update', $reminder) }}" class="reminder-form reminder-edit">
                                    @csrf @method('PATCH')
                                    <label><span>Nama kejadian</span><input class="field" name="title" maxlength="120" value="{{ $reminder->title }}" required></label>
                                    <label><span>Tanggal</span><input class="field" type="date" name="reminder_date" value="{{ $reminder->reminder_date->toDateString() }}" required></label>
                                    <label><span>Waktu pengingat</span><input class="field" type="time" name="reminder_time" value="{{ substr($reminder->reminder_time, 0, 5) }}" required></label>
                                    <label class="reminder-notes"><span>Catatan</span><textarea class="field textarea" name="notes" maxlength="2000">{{ $reminder->notes }}</textarea></label>
                                    <div class="reminder-actions"><button class="button primary" type="submit">Simpan perubahan</button></div>
                                </form>
                                <form method="post" action="{{ route('reminders.destroy', $reminder) }}" onsubmit="return confirm('Hapus pengingat ini?')">@csrf @method('DELETE')<button class="delete-button" type="submit">Hapus</button></form>
                            </article>
                        @empty<div class="empty">Belum ada pengingat tersimpan. Tambahkan tanggal penting di atas.</div>@endforelse
                    </section>
                </div>
            @else
                <div class="heading"><div><p class="eyebrow">KONFIGURASI & KESIAPAN</p><h1>Pengaturan.</h1><p>Ringkasan integrasi, zona waktu, dan template rutinitas.</p></div></div>
                <div class="stack">
                    <section class="card"><p class="eyebrow">INTEGRASI</p>
                        @foreach($services as $name => $ready)<div class="service"><span>{{ $name }}</span><span class="badge {{ $ready ? 'ok' : 'missing' }}">{{ $ready ? 'TERKONFIGURASI' : 'PERLU DIATUR' }}</span></div>@endforeach
                        <div class="hint" style="padding-top:12px">Status ini memeriksa isi konfigurasi. Koneksi API diverifikasi saat layanan digunakan.</div>
                    </section>
                    <section class="card"><p class="eyebrow">WAKTU LOKAL</p><div class="service"><span>Zona waktu aplikasi</span><strong>{{ $timezone }} · WITA</strong></div><div class="service"><span>Template aktif</span><strong>{{ $templates->count() }}</strong></div></section>
                    <section class="card"><p class="eyebrow">TEMPLATE MINGGUAN</p>
                        @forelse($templates as $template)<div class="template"><strong>{{ $template->name }}</strong><small>{{ collect($template->days_of_week)->map(fn ($day) => ucfirst($day))->join(', ') }} · {{ count($template->activities) }} kegiatan</small></div>
                        @empty<div class="empty">Template belum tersedia. Jalankan <code>php artisan schedule:seed-templates</code>.</div>@endforelse
                    </section>
                </div>
            @endif
            <div class="footer">Ruang Hari · Jadwal dan pemantauan pribadi · WITA</div>
        </main>
    </div>
    <nav class="bottom" aria-label="Navigasi bawah"><a class="{{ $active === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}"><span>⌂</span>Hari ini</a><a class="{{ $active === 'agenda' ? 'active' : '' }}" href="{{ route('agenda') }}"><span>▦</span>Agenda</a><a class="{{ $active === 'progress' ? 'active' : '' }}" href="{{ route('progress') }}"><span>✧</span>Progres</a><a class="{{ $active === 'routines' ? 'active' : '' }}" href="{{ route('routines') }}"><span>↻</span>Rutinitas</a><a class="{{ $active === 'reminders' ? 'active' : '' }}" href="{{ route('reminders') }}"><span>🔔</span>Pengingat</a><a class="{{ $active === 'settings' ? 'active' : '' }}" href="{{ route('settings') }}"><span>⚙</span>Setelan</a></nav>
</body>
</html>
