<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Hasil Asesmen</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1f2937;
            font-size: 12px;
        }
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #4f46e5;
            font-size: 20px;
            margin: 0 0 4px 0;
        }
        .header p {
            margin: 0;
            color: #6b7280;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 3px 0;
        }
        .info-table td.label {
            width: 120px;
            color: #6b7280;
        }
        h2 {
            font-size: 14px;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
            margin-top: 24px;
        }
        table.scores {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.scores th, table.scores td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            text-align: left;
        }
        table.scores th {
            background-color: #f9fafb;
            color: #6b7280;
            font-weight: normal;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
        }
        .recommendation {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background-color: #f9fafb;
        }
        .recommendation .title {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .disclaimer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MindCheck &mdash; Hasil Asesmen Stres</h1>
        <p>Laporan skrining tingkat stres siswa</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama</td>
            <td>: {{ $assessment->student->user->name }}</td>
            <td class="label">Kelas</td>
            <td>: {{ $assessment->student->schoolClass->name }}</td>
        </tr>
        <tr>
            <td class="label">NIS</td>
            <td>: {{ $assessment->student->nis }}</td>
            <td class="label">Tanggal</td>
            <td>: {{ $assessment->completed_at->translatedFormat('d F Y, H:i') }}</td>
        </tr>
    </table>

    <h2>Ringkasan Skor</h2>
    <table class="scores">
        <thead>
            <tr>
                <th>Subskala</th>
                <th>Skor (0-42)</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Depresi</td>
                <td>{{ $assessment->depression_score }}</td>
                <td><span class="badge" style="background-color: {{ $assessment->depression_severity->hexColor() }}">{{ $assessment->depression_severity->label() }}</span></td>
            </tr>
            <tr>
                <td>Kecemasan</td>
                <td>{{ $assessment->anxiety_score }}</td>
                <td><span class="badge" style="background-color: {{ $assessment->anxiety_severity->hexColor() }}">{{ $assessment->anxiety_severity->label() }}</span></td>
            </tr>
            <tr>
                <td>Stres</td>
                <td>{{ $assessment->stress_score }}</td>
                <td><span class="badge" style="background-color: {{ $assessment->stress_severity->hexColor() }}">{{ $assessment->stress_severity->label() }}</span></td>
            </tr>
            <tr>
                <td><strong>Status Keseluruhan</strong></td>
                <td colspan="2"><span class="badge" style="background-color: {{ $assessment->overall_severity->hexColor() }}">{{ $assessment->overall_severity->label() }}</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Rekomendasi</h2>
    @foreach ($recommendations as $recommendation)
        <div class="recommendation">
            <div class="title">{{ $recommendation->title }}</div>
            <div>{{ $recommendation->description }}</div>
        </div>
    @endforeach

    <div class="disclaimer">
        MindCheck adalah alat skrining awal untuk mengenali kecenderungan stres, bukan alat diagnosis klinis.
        Jika memerlukan bantuan lebih lanjut, silakan hubungi Guru BK di sekolah.
    </div>
</body>
</html>
