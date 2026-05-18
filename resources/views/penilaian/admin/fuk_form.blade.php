@extends('layouts.app')

@section('content')
    <div class="rounded-3xl p-6" style="background:#edf2fb;border:2px solid #ccdbfd;">

        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.penilaian.aspek', ['aspek' => $fuk->parameter->indikator->aspect_id, 'year' => $year]) }}"
                    class="px-10 py-3 rounded-full font-extrabold text-xl" style="background:#b8c8f0;">
                    Kembali
                </a>

                <div class="px-10 py-3 rounded-full font-extrabold text-2xl" style="background:#abc4ff;">
                    Penilaian FUK
                </div>
            </div>

            <div class="text-xl font-bold text-gray-700">
                Tahun {{ $year }}
            </div>
        </div>

        <div id="ajax-message" class="hidden mb-6 rounded-2xl px-5 py-4 font-semibold"></div>

        @if (session('success'))
            <div class="mb-6 rounded-2xl px-5 py-4 bg-green-100 text-green-800 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-2xl px-5 py-4 bg-red-100 text-red-700 font-semibold">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl px-5 py-4 bg-red-100 text-red-700">
                <div class="font-bold mb-2">Terjadi kesalahan:</div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-5 rounded-2xl px-6 py-5 bg-white" style="border:2px solid #214f7a;">
            <div class="grid grid-cols-12 gap-4 items-center">
                <div class="col-span-8">
                    <div class="text-lg font-bold text-gray-700 mb-1">FUK</div>
                    <div class="text-xl font-semibold">
                        {{ $fuk->id }}. {{ $fuk->name }}
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                        Tipe penilaian: {{ $fuk->tipe_penilaian ?? '-' }}
                    </div>
                </div>

                <div class="col-span-1 text-center">
                    <div class="text-sm font-bold mb-1">Bobot</div>
                    <div class="h-12 rounded-xl flex items-center justify-center text-lg font-semibold"
                        style="border:2px solid #214f7a;">
                        {{ $fuk->bobot ?? '-' }}
                    </div>
                </div>

                <div class="col-span-1 text-center">
                    <div class="text-sm font-bold mb-1">Score</div>
                    <div class="h-12 rounded-xl flex items-center justify-center text-lg font-semibold"
                        style="border:2px solid #214f7a;" id="current-leaf-score">
                        {{ $summary['fuk']['score'] ?? '-' }}
                    </div>
                </div>

                <div class="col-span-1 text-center">
                    <div class="text-sm font-bold mb-1">%</div>
                    <div class="h-12 rounded-xl flex items-center justify-center text-lg font-semibold"
                        style="border:2px solid #214f7a;" id="current-leaf-percent">
                        {{ $summary['fuk']['percent'] ?? '-' }}
                    </div>
                </div>

                <div class="col-span-1 text-center">
                    <div class="text-sm font-bold mb-1">Action</div>
                    <div class="h-12 rounded-xl flex items-center justify-center text-lg font-semibold text-white"
                        style="background:#164b7a;">
                        ▼
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full flex justify-center mb-4">
            <div class="w-0 h-0 border-l-[18px] border-r-[18px] border-t-[28px] border-l-transparent border-r-transparent"
                style="border-top-color:#000;">
            </div>
        </div>

        <form method="POST" action="{{ route('admin.penilaian.fuk.save', $fuk->id) }}" id="fukForm">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="rounded-2xl overflow-hidden" style="border:2px solid #1a214f; background:white;">
                <div class="px-6 py-3 text-center text-white font-bold text-2xl" style="background:#17607a;">
                    ASSES PEMENUHAN FUK
                </div>

                <div class="p-6">

                    <div class="grid grid-cols-12 gap-4 mb-4 items-center">
                        <label class="col-span-2 text-lg font-semibold">Nama Dokumen :</label>
                        <div class="col-span-4">
                            <input type="text" name="document_name" id="document_name"
                                value="{{ old('document_name', $score->document_name ?? '') }}"
                                class="w-full h-12 rounded-md px-4 outline-none"
                                style="border:2px solid #383b7a;">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-4 mb-4 items-start">
                        <label class="col-span-2 text-lg font-semibold pt-3">Dokumen :</label>

                        <div class="col-span-10">
                            <div class="mb-3">
                                <select id="document_select" class="w-full h-12 rounded-md px-4 outline-none"
                                    style="border:2px solid #383b7a;">
                                    <option value="">-- pilih dokumen --</option>
                                    @foreach ($documents as $doc)
                                        @php
                                            $url = asset('storage/' . $doc->file_path);
                                            $ext = strtolower(pathinfo($doc->original_name ?? $doc->file_path, PATHINFO_EXTENSION));
                                        @endphp
                                        <option
                                            value="{{ $url }}"
                                            data-name="{{ $doc->original_name }}"
                                            data-ext="{{ $ext }}">
                                            {{ $doc->original_name }}{{ $doc->division?->name ? ' - ' . $doc->division->name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="rounded-md overflow-hidden" style="border:2px solid #383b7a;">
                                <div class="flex items-center gap-4 px-5 py-3 flex-wrap" style="background:#d9d9d9;">
                                    <button type="button" id="btn-first-page"
                                        class="leading-none text-[28px] font-normal"
                                        title="Ke halaman pertama">
                                        📖
                                    </button>

                                    <button type="button" id="btn-search"
                                        class="leading-none text-[28px] font-normal"
                                        title="Cari teks">
                                        🔍
                                    </button>

                                    <div class="text-[20px] font-semibold leading-none flex items-center gap-2">
                                        <span id="page-num">0</span>
                                        <span>of</span>
                                        <span id="page-count">0</span>
                                    </div>

                                    <button type="button" id="btn-prev-page"
                                        class="leading-none text-[24px] font-bold px-1"
                                        title="Halaman sebelumnya">
                                        &lt;
                                    </button>

                                    <button type="button" id="btn-next-page"
                                        class="leading-none text-[24px] font-bold px-1"
                                        title="Halaman berikutnya">
                                        &gt;
                                    </button>

                                    <div class="text-[20px] font-semibold leading-none ml-2">
                                        <span id="zoom-label">100%</span>
                                    </div>

                                    <div class="ml-auto flex items-center gap-3">
                                        <select id="zoom_select"
                                            class="px-4 py-2 rounded-md bg-white text-[16px] font-medium"
                                            style="border:2px solid #383b7a; min-width: 190px;">
                                            <option value="0.75">75%</option>
                                            <option value="1" selected>100%</option>
                                            <option value="1.25">125%</option>
                                            <option value="1.5">150%</option>
                                            <option value="2">200%</option>
                                        </select>

                                        <a href="#"
                                            id="btn-open-file"
                                            target="_blank"
                                            class="px-4 py-2 rounded-md bg-white text-[16px] font-semibold"
                                            style="border:2px solid #383b7a;">
                                            Buka File
                                        </a>
                                    </div>
                                </div>

                                <div class="bg-white p-2" id="pdf-search-box" style="display:none; border-top:1px solid #cfcfcf;">
                                    <div class="flex items-center gap-3">
                                        <input type="text" id="search_text"
                                            class="w-full h-11 rounded-md px-4 outline-none"
                                            placeholder="Cari kata dalam dokumen..."
                                            style="border:2px solid #383b7a;">

                                        <button type="button" id="btn-search-run"
                                            class="px-5 py-2 rounded-md text-white font-semibold"
                                            style="background:#17607a;">
                                            Cari
                                        </button>

                                        <button type="button" id="btn-search-close"
                                            class="px-5 py-2 rounded-md font-semibold"
                                            style="background:#d7d7d7;">
                                            Tutup
                                        </button>
                                    </div>
                                </div>

                                <div id="viewer-message" class="hidden px-4 py-3 text-sm font-medium bg-yellow-50 text-yellow-800 border-t border-yellow-200"></div>

                                <div class="w-full relative bg-[#f7f7f7]" style="height:520px;" id="viewer-shell">
                                    <div id="pdf-container" class="w-full h-full overflow-y-auto overflow-x-auto hidden px-4 py-4">
                                        <canvas id="pdf-canvas" class="block mx-auto"></canvas>
                                    </div>

                                    <div id="image-container" class="w-full h-full overflow-y-auto overflow-x-auto hidden p-4">
                                        <img id="image-viewer" src="" alt="Preview gambar"
                                            class="block mx-auto"
                                            style="max-width:none; transform-origin: top center;">
                                    </div>

                                    <div id="office-container" class="w-full h-full hidden">
                                        <iframe id="office-frame" src="" class="w-full h-full border-0"></iframe>
                                    </div>

                                    <div id="generic-container" class="w-full h-full hidden p-8 flex flex-col items-center justify-center text-center">
                                        <div class="text-6xl mb-4">📄</div>
                                        <div class="text-xl font-bold mb-2">Preview tidak tersedia penuh untuk file ini</div>
                                        <div class="text-gray-600 mb-4">
                                            File masih bisa dibuka di tab baru atau diunduh.
                                        </div>
                                        <div class="flex gap-3">
                                            <a href="#" id="generic-open-link" target="_blank"
                                                class="px-5 py-3 rounded-md text-white font-semibold"
                                                style="background:#17607a;">
                                                Buka File
                                            </a>
                                            <a href="#" id="generic-download-link"
                                                download
                                                class="px-5 py-3 rounded-md font-semibold"
                                                style="background:#d7d7d7;">
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-4 mb-6 items-center">
                        <label class="col-span-2 text-lg font-semibold">Halaman :</label>
                        <div class="col-span-2">
                            <input type="text" name="page_reference" id="page_reference"
                                value="{{ old('page_reference', $score->page_reference ?? '') }}"
                                class="w-full h-12 rounded-md px-4 outline-none"
                                style="border:2px solid #383b7a;">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-4 mb-6 items-start">
                        <label class="col-span-2 text-lg font-semibold pt-3">Penjelasan :</label>
                        <div class="col-span-10">
                            <textarea name="explanation" rows="4" class="w-full rounded-md px-4 py-3 outline-none"
                                style="border:2px solid #383b7a;">{{ old('explanation', $score->explanation ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-4 mb-6 items-center">
                        <label class="col-span-2 text-lg font-semibold">Penilaian :</label>
                        <div class="col-span-10">
                            <div class="flex items-center gap-12 flex-wrap">
                                <label class="inline-flex items-center gap-3 text-xl font-medium cursor-pointer"
                                    title="Tidak ada">
                                    <input type="radio" name="score" value="0" data-percent="0"
                                        {{ (string) old('score', $score->score ?? '') === '0' ? 'checked' : '' }}
                                        class="w-7 h-7 score-radio">
                                    0%
                                </label>

                                <label class="inline-flex items-center gap-3 text-xl font-medium cursor-pointer"
                                    title="Memenuhi sebagian kecil (>0 s.d. 50%) persyaratan / kriteria">
                                    <input type="radio" name="score" value="0.25" data-percent="25"
                                        {{ (string) old('score', $score->score ?? '') === '0.25' ? 'checked' : '' }}
                                        class="w-7 h-7 score-radio">
                                    25%
                                </label>

                                <label class="inline-flex items-center gap-3 text-xl font-medium cursor-pointer"
                                    title="Memenuhi sebagian (>50 s.d. 75%) persyaratan / kriteria">
                                    <input type="radio" name="score" value="0.5" data-percent="50"
                                        {{ (string) old('score', $score->score ?? '') === '0.5' ? 'checked' : '' }}
                                        class="w-7 h-7 score-radio">
                                    50%
                                </label>

                                <label class="inline-flex items-center gap-3 text-xl font-medium cursor-pointer"
                                    title="Memenuhi sebagian besar (>75 s.d. 85%) persyaratan / kriteria">
                                    <input type="radio" name="score" value="0.75" data-percent="75"
                                        {{ (string) old('score', $score->score ?? '') === '0.75' ? 'checked' : '' }}
                                        class="w-7 h-7 score-radio">
                                    75%
                                </label>

                                <label class="inline-flex items-center gap-3 text-xl font-medium cursor-pointer"
                                    title="Memenuhi seluruh persyaratan / kriteria">
                                    <input type="radio" name="score" value="1" data-percent="100"
                                        {{ (string) old('score', $score->score ?? '') === '1' ? 'checked' : '' }}
                                        class="w-7 h-7 score-radio">
                                    100%
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-4 mb-6 items-start">
                        <label class="col-span-2 text-lg font-semibold pt-3">Review assesor :</label>
                        <div class="col-span-10">
                            <textarea name="assessor_review" rows="4" class="w-full rounded-md px-4 py-3 outline-none"
                                style="border:2px solid #383b7a;">{{ old('assessor_review', $score->assessor_review ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-4 mb-8 items-start">
                        <label class="col-span-2 text-lg font-semibold pt-3">Rekomendasi :</label>
                        <div class="col-span-10">
                            <textarea name="recommendation" rows="4" class="w-full rounded-md px-4 py-3 outline-none"
                                style="border:2px solid #383b7a;">{{ old('recommendation', $score->recommendation ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-6">
                        <a href="{{ route('admin.penilaian.aspek', ['aspek' => $fuk->parameter->indikator->aspect_id, 'year' => $year]) }}"
                            class="px-10 py-3 rounded-full text-2xl font-bold text-black"
                            style="background:#d7d7d7;">
                            Tutup
                        </a>

                        <button type="submit" class="px-10 py-3 rounded-full text-2xl font-bold text-white"
                            style="background:#0f5e9c;" id="saveBtn">
                            Simpan
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        if (window.pdfjsLib) {
            pdfjsLib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }

        const documentSelect = document.getElementById('document_select');
        const documentNameInput = document.getElementById('document_name');
        const form = document.getElementById('fukForm');
        const saveBtn = document.getElementById('saveBtn');
        const ajaxMessage = document.getElementById('ajax-message');
        const scoreRadios = document.querySelectorAll('.score-radio');
        const currentLeafScore = document.getElementById('current-leaf-score');
        const currentLeafPercent = document.getElementById('current-leaf-percent');

        const canvas = document.getElementById('pdf-canvas');
        const ctx = canvas.getContext('2d');

        const pdfContainer = document.getElementById('pdf-container');
        const imageContainer = document.getElementById('image-container');
        const officeContainer = document.getElementById('office-container');
        const genericContainer = document.getElementById('generic-container');

        const imageViewer = document.getElementById('image-viewer');
        const officeFrame = document.getElementById('office-frame');

        const genericOpenLink = document.getElementById('generic-open-link');
        const genericDownloadLink = document.getElementById('generic-download-link');
        const btnOpenFile = document.getElementById('btn-open-file');

        const btnFirstPage = document.getElementById('btn-first-page');
        const btnSearch = document.getElementById('btn-search');
        const btnSearchRun = document.getElementById('btn-search-run');
        const btnSearchClose = document.getElementById('btn-search-close');
        const btnPrevPage = document.getElementById('btn-prev-page');
        const btnNextPage = document.getElementById('btn-next-page');

        const searchBox = document.getElementById('pdf-search-box');
        const searchText = document.getElementById('search_text');
        const zoomSelect = document.getElementById('zoom_select');
        const zoomLabel = document.getElementById('zoom-label');
        const pageNum = document.getElementById('page-num');
        const pageCount = document.getElementById('page-count');
        const pageReference = document.getElementById('page_reference');
        const viewerMessage = document.getElementById('viewer-message');

        let pdfDoc = null;
        let currentPage = 1;
        let currentScale = 1;
        let currentUrl = null;
        let currentType = null;
        let imageScale = 1;

        const pdfExtensions = ['pdf'];
        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
        const officeExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

        function setText(id, value) {
            const el = document.getElementById(id);
            if (el) el.textContent = value ?? '-';
        }

        function showMessage(type, text) {
            ajaxMessage.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-700');
            ajaxMessage.classList.add(type === 'success' ? 'bg-green-100' : 'bg-red-100');
            ajaxMessage.classList.add(type === 'success' ? 'text-green-800' : 'text-red-700');
            ajaxMessage.textContent = text;
        }

        function showViewerMessage(text = '') {
            if (!text) {
                viewerMessage.classList.add('hidden');
                viewerMessage.textContent = '';
                return;
            }

            viewerMessage.classList.remove('hidden');
            viewerMessage.textContent = text;
        }

        function resetCounters() {
            pageNum.textContent = '0';
            pageCount.textContent = '0';
            zoomLabel.textContent = '100%';
        }

        function hideAllViewers() {
            pdfContainer.classList.add('hidden');
            imageContainer.classList.add('hidden');
            officeContainer.classList.add('hidden');
            genericContainer.classList.add('hidden');
        }

        function resetViewerState() {
            pdfDoc = null;
            currentPage = 1;
            currentScale = 1;
            imageScale = 1;
            currentUrl = null;
            currentType = null;
            canvas.width = 0;
            canvas.height = 0;
            imageViewer.src = '';
            imageViewer.style.transform = 'scale(1)';
            officeFrame.src = '';
            searchBox.style.display = 'none';
            resetCounters();
            showViewerMessage('');
        }

        function detectFileType(ext) {
            ext = (ext || '').toLowerCase();

            if (pdfExtensions.includes(ext)) return 'pdf';
            if (imageExtensions.includes(ext)) return 'image';
            if (officeExtensions.includes(ext)) return 'office';
            return 'generic';
        }

        function updateOpenLinks(url) {
            btnOpenFile.href = url || '#';
            genericOpenLink.href = url || '#';
            genericDownloadLink.href = url || '#';
        }

        function updateToolbarAvailability() {
            const isPdf = currentType === 'pdf';
            const isImage = currentType === 'image';

            btnSearch.disabled = !isPdf;
            btnSearchRun.disabled = !isPdf;
            btnPrevPage.disabled = !(isPdf && pdfDoc && currentPage > 1);
            btnNextPage.disabled = !(isPdf && pdfDoc && currentPage < pdfDoc.numPages);
            btnFirstPage.disabled = !(isPdf || isImage);
            zoomSelect.disabled = !(isPdf || isImage);
        }

        async function renderPdfPage(pageNumber) {
            if (!pdfDoc) return;

            const page = await pdfDoc.getPage(pageNumber);
            const viewport = page.getViewport({ scale: currentScale });

            canvas.width = viewport.width;
            canvas.height = viewport.height;

            await page.render({
                canvasContext: ctx,
                viewport
            }).promise;

            currentPage = pageNumber;
            pageNum.textContent = currentPage;
            pageCount.textContent = pdfDoc.numPages;
            pageReference.value = currentPage;
            zoomLabel.textContent = Math.round(currentScale * 100) + '%';

            updateToolbarAvailability();
        }

        async function loadPdf(url) {
            try {
                resetViewerState();
                hideAllViewers();

                currentType = 'pdf';
                currentUrl = url;
                pdfContainer.classList.remove('hidden');
                updateOpenLinks(url);

                const loadingTask = pdfjsLib.getDocument({
                    url: url,
                    withCredentials: false
                });

                pdfDoc = await loadingTask.promise;
                zoomSelect.value = '1';
                await renderPdfPage(1);

                showViewerMessage('');
            } catch (error) {
                hideAllViewers();
                genericContainer.classList.remove('hidden');
                currentType = 'generic';
                updateOpenLinks(url);
                showViewerMessage('PDF gagal dimuat di viewer. File tetap bisa dibuka lewat tombol "Buka File".');
                updateToolbarAvailability();
            }
        }

        function loadImage(url) {
            resetViewerState();
            hideAllViewers();

            currentType = 'image';
            currentUrl = url;
            imageContainer.classList.remove('hidden');
            updateOpenLinks(url);
            zoomSelect.value = '1';

            imageViewer.onload = function () {
                pageNum.textContent = '1';
                pageCount.textContent = '1';
                pageReference.value = '1';
                zoomLabel.textContent = '100%';
                updateToolbarAvailability();
            };

            imageViewer.onerror = function () {
                hideAllViewers();
                genericContainer.classList.remove('hidden');
                currentType = 'generic';
                showViewerMessage('Gambar gagal dimuat. File tetap bisa dibuka lewat tombol "Buka File".');
                updateToolbarAvailability();
            };

            imageViewer.src = url;
            imageViewer.style.transform = 'scale(1)';
            showViewerMessage('Mode gambar aktif. Fitur cari teks hanya tersedia untuk PDF.');
            updateToolbarAvailability();
        }

        function loadOffice(url) {
            resetViewerState();
            hideAllViewers();

            currentType = 'office';
            currentUrl = url;
            officeContainer.classList.remove('hidden');
            updateOpenLinks(url);

            const officeViewerUrl = 'https://view.officeapps.live.com/op/embed.aspx?src=' + encodeURIComponent(url);
            officeFrame.src = officeViewerUrl;

            pageNum.textContent = '-';
            pageCount.textContent = '-';
            pageReference.value = '';
            zoomLabel.textContent = 'Auto';

            showViewerMessage('Mode Office aktif. Search, page, dan zoom toolbar kustom tidak selalu tersedia untuk Word, Excel, atau PowerPoint.');
            updateToolbarAvailability();
        }

        function loadGeneric(url) {
            resetViewerState();
            hideAllViewers();

            currentType = 'generic';
            currentUrl = url;
            genericContainer.classList.remove('hidden');
            updateOpenLinks(url);

            showViewerMessage('Tipe file ini belum punya preview interaktif penuh. Gunakan tombol "Buka File".');
            updateToolbarAvailability();
        }

        async function updatePreview() {
            const option = documentSelect.options[documentSelect.selectedIndex];
            const url = option ? option.value : '';
            const name = option ? option.dataset.name || '' : '';
            const ext = option ? option.dataset.ext || '' : '';

            if (name && !documentNameInput.value) {
                documentNameInput.value = name;
            }

            if (!url) {
                resetViewerState();
                hideAllViewers();
                genericContainer.classList.remove('hidden');
                showViewerMessage('Pilih dokumen terlebih dahulu.');
                updateToolbarAvailability();
                return;
            }

            const type = detectFileType(ext);
            updateOpenLinks(url);

            if (type === 'pdf') {
                await loadPdf(url);
                return;
            }

            if (type === 'image') {
                loadImage(url);
                return;
            }

            if (type === 'office') {
                loadOffice(url);
                return;
            }

            loadGeneric(url);
        }

        btnFirstPage.addEventListener('click', async () => {
            if (currentType === 'pdf' && pdfDoc) {
                await renderPdfPage(1);
                pdfContainer.scrollTop = 0;
                pdfContainer.scrollLeft = 0;
                return;
            }

            if (currentType === 'image') {
                imageScale = 1;
                imageViewer.style.transform = 'scale(1)';
                zoomLabel.textContent = '100%';
                imageContainer.scrollTop = 0;
                imageContainer.scrollLeft = 0;
            }
        });

        btnPrevPage.addEventListener('click', async () => {
            if (currentType !== 'pdf' || !pdfDoc || currentPage <= 1) return;
            await renderPdfPage(currentPage - 1);
        });

        btnNextPage.addEventListener('click', async () => {
            if (currentType !== 'pdf' || !pdfDoc || currentPage >= pdfDoc.numPages) return;
            await renderPdfPage(currentPage + 1);
        });

        zoomSelect.addEventListener('change', async function () {
            const selectedScale = parseFloat(this.value);

            if (currentType === 'pdf' && pdfDoc) {
                currentScale = selectedScale;
                await renderPdfPage(currentPage);
                return;
            }

            if (currentType === 'image') {
                imageScale = selectedScale;
                imageViewer.style.transform = `scale(${imageScale})`;
                zoomLabel.textContent = Math.round(imageScale * 100) + '%';
            }
        });

        btnSearch.addEventListener('click', () => {
            if (currentType !== 'pdf') return;
            searchBox.style.display = searchBox.style.display === 'none' ? 'block' : 'none';
            if (searchBox.style.display === 'block') {
                searchText.focus();
            }
        });

        btnSearchClose.addEventListener('click', () => {
            searchBox.style.display = 'none';
            searchText.value = '';
        });

        btnSearchRun.addEventListener('click', async () => {
            if (currentType !== 'pdf' || !pdfDoc) {
                showMessage('error', 'Fitur cari teks hanya untuk PDF.');
                return;
            }

            const keyword = (searchText.value || '').trim().toLowerCase();
            if (!keyword) {
                showMessage('error', 'Masukkan kata yang ingin dicari.');
                return;
            }

            for (let i = 1; i <= pdfDoc.numPages; i++) {
                const page = await pdfDoc.getPage(i);
                const textContent = await page.getTextContent();
                const pageText = textContent.items.map(item => item.str).join(' ').toLowerCase();

                if (pageText.includes(keyword)) {
                    await renderPdfPage(i);
                    showMessage('success', `Kata "${keyword}" ditemukan di halaman ${i}.`);
                    return;
                }
            }

            showMessage('error', `Kata "${keyword}" tidak ditemukan di dokumen.`);
        });

        searchText.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                btnSearchRun.click();
            }
        });

        scoreRadios.forEach(radio => {
            radio.addEventListener('change', function () {
                currentLeafScore.textContent = this.value ?? '-';
                currentLeafPercent.textContent = this.dataset.percent ?? '-';
            });
        });

        if (documentSelect) {
            documentSelect.addEventListener('change', updatePreview);
            updatePreview();
        }

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            saveBtn.disabled = true;
            saveBtn.textContent = 'Menyimpan...';

            try {
                const formData = new FormData(form);

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Gagal menyimpan penilaian.');
                }

                const data = result.data;
                setText('current-leaf-score', data.fuk.score);
                setText('current-leaf-percent', data.fuk.percent);

                showMessage('success', result.message);
            } catch (error) {
                showMessage('error', error.message);
            } finally {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Simpan';
            }
        });
    </script>
@endsection