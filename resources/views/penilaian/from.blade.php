<x-app-layout>

<div class="container py-4">

    <h3 class="fw-bold mb-3">Penilaian</h3>
    <h5 class="mb-4 text-secondary">Parameter: {{ $parameter->nama_parameter }}</h5>

    <div class="card shadow-sm p-4 border-0">

        <form action="{{ route('penilaian.simpan', $parameter->id) }}" method="POST">
            @csrf

            <!-- Nilai -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Nilai</label>
                <select name="nilai" class="form-select" required>
                    <option value="">-- Pilih Nilai --</option>
                    <option value="1">1 - Tidak Baik</option>
                    <option value="2">2 - Kurang</option>
                    <option value="3">3 - Cukup</option>
                    <option value="4">4 - Baik</option>
                    <option value="5">5 - Sangat Baik</option>
                </select>
            </div>

            <!-- Catatan -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Catatan (Opsional)</label>
                <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan..."></textarea>
            </div>

            <button class="btn btn-dark w-100 mt-3">Simpan Penilaian</button>

        </form>

    </div>

</div>

</x-app-layout>
