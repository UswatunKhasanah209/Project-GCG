@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white px-6 py-6">
    <div class="max-w-4xl mx-auto">

        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('profile.index') }}" class="text-[#06496b] hover:opacity-80">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-[#06496b]">Histori Download</h1>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
            @forelse($histories as $history)
                <div class="flex items-start gap-4 py-5 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="w-12 h-12 rounded-2xl bg-[#eef8fb] text-[#2e7892] flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16"/>
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-[#06496b] text-lg break-words">
                            {{ $history->file_name ?? $history->document?->original_name ?? 'File tidak diketahui' }}
                        </div>

                        <div class="text-sm text-gray-500 mt-1">
                            Didownload:
                            {{ $history->downloaded_at ? $history->downloaded_at->format('d M Y H:i') : '-' }}
                        </div>

                        @if($history->document)
                            <a
                                href="{{ route('library.documents.download', $history->document->id) }}"
                                class="inline-flex items-center mt-3 text-sm font-bold text-[#2e7892] hover:underline"
                            >
                                Download ulang
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-16">
                    <div class="mx-auto w-20 h-20 rounded-full bg-[#eef8fb] text-[#2e7892] flex items-center justify-center mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-11 h-11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 109-9"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4v5h5"/>
                        </svg>
                    </div>

                    <div class="text-xl font-bold text-[#06496b]">Belum ada histori download</div>
                    <p class="text-gray-500 mt-2">File yang kamu download akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $histories->links() }}
        </div>

    </div>
</div>
@endsection