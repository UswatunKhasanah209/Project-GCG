<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan GCG</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 28px;
        }

        h1 {
            font-size: 26px;
            margin: 0 0 14px 0;
        }

        h2 {
            font-size: 16px;
            margin: 0 0 6px 0;
        }

        .meta {
            margin-bottom: 18px;
            line-height: 1.6;
        }

        .card {
            border: 1px solid #9fb7d9;
            border-radius: 8px;
            margin-bottom: 18px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .card-head {
            background: #dbe7ff;
            padding: 10px 14px;
            font-weight: bold;
            font-size: 13px;
        }

        .card-body {
            padding: 12px 14px;
        }

        .row {
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .box {
            border: 1px solid #c8d6ea;
            background: #f8fbff;
            padding: 8px 10px;
            min-height: 22px;
        }

        .score-box {
            display: inline-block;
            border: 1px solid #7c98c2;
            background: #eef4ff;
            padding: 6px 12px;
            font-weight: bold;
            border-radius: 6px;
        }

        .empty {
            text-align: center;
            border: 1px solid #9fb7d9;
            padding: 16px;
            background: #f8fbff;
        }
    </style>
</head>
<body>
    <h1>Laporan GCG</h1>

    <div class="meta">
        <h2>Tahun {{ $year }}</h2>
        <div>Jenis: <strong>{{ $type === 'all' ? 'Keseluruhan' : 'Per Divisi' }}</strong></div>
        <div>Akses: <strong>{{ $user->role === 'admin' ? 'Admin' : 'User / Divisi Sendiri' }}</strong></div>
        @isset($selectedDivisionName)
            @if($selectedDivisionName)
                <div>Divisi: <strong>{{ $selectedDivisionName }}</strong></div>
            @endif
        @endisset
    </div>

    @forelse($rows as $index => $row)
        <div class="card">
            <div class="card-head">
                Item {{ $index + 1 }} - {{ $row['fuk'] ?? '-' }}
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="label">Divisi</div>
                    <div class="box">{{ $row['division'] ?? '-' }}</div>
                </div>

                <div class="row">
                    <div class="label">Aspek</div>
                    <div class="box">{{ $row['aspek'] ?? '-' }}</div>
                </div>

                <div class="row">
                    <div class="label">Indikator</div>
                    <div class="box">{{ $row['indikator'] ?? '-' }}</div>
                </div>

                <div class="row">
                    <div class="label">Parameter</div>
                    <div class="box">{{ $row['parameter'] ?? '-' }}</div>
                </div>

                <div class="row">
                    <div class="label">Dokumen</div>
                    <div class="box">{{ $row['dokumen'] ?? '-' }}</div>
                </div>

                <div class="row">
                    <div class="label">Halaman</div>
                    <div class="box">{{ $row['halaman'] ?? '-' }}</div>
                </div>

                <div class="row">
                    <div class="label">Penjelasan</div>
                    <div class="box">{{ $row['penjelasan'] ?? '-' }}</div>
                </div>

                <div class="row">
                    <div class="label">Penilaian</div>
                    <div class="score-box">
                        {{ $row['score_percent'] ?? '-' }} (score: {{ $row['score'] ?? '-' }})
                    </div>
                </div>

                <div class="row">
                    <div class="label">Review Assessor</div>
                    <div class="box">{{ $row['review'] ?? '-' }}</div>
                </div>

                <div class="row">
                    <div class="label">Rekomendasi</div>
                    <div class="box">{{ $row['rekomendasi'] ?? '-' }}</div>
                </div>
            </div>
        </div>
    @empty
        <div class="empty">
            Belum ada data penilaian.
        </div>
    @endforelse
</body>
</html>