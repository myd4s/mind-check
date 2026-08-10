<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Asesmen - {{ $student->user->name }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 18px; margin: 0 0 4px; color: #0f766e; }
        .subtitle { font-size: 11px; color: #6b7280; margin-bottom: 20px; }
        .profile-table { width: 100%; margin-bottom: 20px; }
        .profile-table td { padding: 3px 0; vertical-align: top; }
        .profile-label { color: #6b7280; width: 120px; }
        .profile-value { font-weight: bold; }
        table.results { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.results th, table.results td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; font-size: 11px; }
        table.results th { background-color: #f3f4f6; text-transform: uppercase; font-size: 9px; color: #6b7280; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 8px; color: #ffffff; font-size: 10px; }
        .badge-rendah { background-color: #16a34a; }
        .badge-sedang { background-color: #d97706; }
        .badge-tinggi { background-color: #dc2626; }
        h2 { font-size: 13px; color: #111827; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin-top: 24px; }
        .note-block { margin-bottom: 12px; }
        .note-meta { font-size: 10px; color: #9ca3af; margin-top: 2px; }
        .footer { margin-top: 30px; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <h1>Laporan Hasil Asesmen Stress</h1>
    <p class="subtitle">MindCare &middot; Dicetak pada {{ now()->translatedFormat('d M Y H:i') }}</p>

    <table class="profile-table">
        <tr>
            <td class="profile-label">Nama</td>
            <td class="profile-value">{{ $student->user->name }}</td>
        </tr>
        <tr>
            <td class="profile-label">NISN</td>
            <td class="profile-value">{{ $student->nisn }}</td>
        </tr>
        <tr>
            <td class="profile-label">Jenis Kelamin</td>
            <td class="profile-value">{{ $student->gender->label() }}</td>
        </tr>
        <tr>
            <td class="profile-label">Kelas</td>
            <td class="profile-value">{{ $student->currentClassHistory?->schoolClass?->name ?? '—' }}</td>
        </tr>
    </table>

    <h2>Histori Skor</h2>
    <table class="results">
        <thead>
            <tr>
                <th>Tanggal Selesai</th>
                <th>Jadwal</th>
                <th>Assessment</th>
                <th>Skor</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($results as $result)
                <tr>
                    <td>{{ $result->completed_at->translatedFormat('d M Y H:i') }}</td>
                    <td>{{ $result->assessmentSchedule->title }}</td>
                    <td>{{ $result->assessmentSchedule->assessment->title }}</td>
                    <td>{{ $result->total_score }}</td>
                    <td><span class="badge badge-{{ $result->category }}">{{ ucfirst($result->category) }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada hasil asesmen.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Catatan Guru BK</h2>
    @php $notedResults = $results->filter(fn ($r) => $r->note); @endphp
    @forelse ($notedResults as $result)
        <div class="note-block">
            <strong>{{ $result->assessmentSchedule->title }}</strong> ({{ $result->completed_at->translatedFormat('d M Y') }})
            <p>{{ $result->note->content }}</p>
            <p class="note-meta">Ditulis oleh {{ $result->note->guruBk->name }} pada {{ $result->note->updated_at->translatedFormat('d M Y H:i') }}</p>
        </div>
    @empty
        <p>Belum ada catatan dari Guru BK.</p>
    @endforelse

    <p class="footer">Dokumen ini digenerate otomatis oleh sistem MindCare.</p>
</body>
</html>
