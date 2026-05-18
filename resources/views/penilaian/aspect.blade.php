@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h3 class="fw-bold mb-4">Aspek: {{ $aspect->nama_aspek }}</h3>

    <div class="card p-3 shadow-sm border-0">
        <h5 class="mb-3">Daftar Parameter</h5>

        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Parameter</th>
                    <th style="width: 130px;">Evidence</th>
                    <th style="width: 130px;">Status Nilai</th>
                    <th style="width: 200px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($parameters as $param)
                @php
                    $evaluation = $param->evaluationForUser(auth()->id());
                    $hasEvidence = $evaluation && $evaluation->evidence_file;
                    $isCompleted = $evaluation && $evaluation->nilai_final;
                @endphp

                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>
                        <strong>{{ $param->name }}</strong><br>
                        <small class="text-muted">{{ $param->description }}</small>
                    </td>

                    <!-- Status Evidence -->
                    <td class="text-center">
                        @if ($hasEvidence)
                            <span class="badge bg-success">Lengkap</span><br>
                            <a href="{{ asset('storage/'.$evaluation->evidence_file) }}" 
                               target="_blank"
                               class="small">Lihat</a>
                        @else
                            <span class="badge bg-danger">Belum Ada</span>
                        @endif
                    </td>

                    <!-- Status Nilai -->
                    <td class="text-center">
                        @if ($isCompleted)
                            <span class="badge bg-success">Selesai</span><br>
                            <small>Nilai: {{ $evaluation->nilai_final }}</small>
                        @else
                            <span class="badge bg-warning">Belum Dinilai</span>
                        @endif
                    </td>

                    <!-- Action Buttons -->
                    <td class="text-center">

                        <!-- Upload Evidence -->
                        <a href="{{ route('penilaian.parameter', $param->id) }}"
                           class="btn btn-outline-primary btn-sm w-100 mb-1">
                            Upload Evidence
                        </a>

                        <!-- Nilai Parameter -->
                        <a href="{{ route('penilaian.parameter', $param->id) }}"
                           class="btn btn-dark btn-sm w-100">
                            Nilai Parameter
                        </a>

                    </td>
                </tr>

                @endforeach
            </tbody>
        </table>

        <a href="{{ route('penilaian.index') }}" class="btn btn-secondary mt-3">
            ← Kembali ke Aspek
        </a>

    </div>

</div>
@endsection
