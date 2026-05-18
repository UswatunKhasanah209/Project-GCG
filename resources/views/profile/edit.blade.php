@extends('layouts.app')

@section('content')
@php
    $avatarUrl = $user->avatar
        ? asset('storage/' . $user->avatar) . '?v=' . optional($user->updated_at)->timestamp
        : null;
@endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">

<div class="min-h-screen bg-white px-6 py-6">
    <div class="max-w-3xl mx-auto">

        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('profile.account') }}" class="text-[#06496b] hover:opacity-80">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>

                <h1 class="text-2xl font-bold text-[#06496b]">Edit Profil</h1>
            </div>

            <button form="profileForm" type="submit" class="text-green-600 hover:opacity-80">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </div>

        <div id="successMessage" class="hidden mb-5 rounded-2xl bg-green-50 border border-green-200 text-green-700 px-5 py-4 font-semibold"></div>
        <div id="errorMessage" class="hidden mb-5 rounded-2xl bg-red-50 border border-red-200 text-red-700 px-5 py-4 font-semibold"></div>

        <form id="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="max-w-xl mx-auto">
            @csrf
            @method('PATCH')

            <input type="hidden" name="cropped_avatar" id="croppedAvatar">

            <div class="flex flex-col items-center mb-10">
                <div class="relative">
                    @if($avatarUrl)
                        <img
                            id="avatarPreview"
                            src="{{ $avatarUrl }}"
                            class="w-32 h-32 rounded-full object-cover border-[4px] border-[#2e7892]"
                            alt="Avatar"
                        >
                    @else
                        <div id="avatarEmpty" class="w-32 h-32 rounded-full border-[4px] border-[#2e7892] flex items-center justify-center text-[#2e7892]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5a7.5 7.5 0 0115 0"/>
                            </svg>
                        </div>

                        <img
                            id="avatarPreview"
                            src=""
                            class="hidden w-32 h-32 rounded-full object-cover border-[4px] border-[#2e7892]"
                            alt="Avatar"
                        >
                    @endif

                    <label for="avatarInput" class="absolute bottom-1 right-1 w-9 h-9 rounded-full bg-white border border-gray-200 shadow flex items-center justify-center cursor-pointer text-[#2e7892]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h4l2-3h6l2 3h4v13H3V7z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 17a4 4 0 100-8 4 4 0 000 8z"/>
                        </svg>
                    </label>

                    <input id="avatarInput" type="file" accept="image/*" class="hidden">
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block mb-2 font-bold text-gray-800">Nama</label>
                    <input
                        id="nameInput"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        class="w-full rounded-xl bg-gray-50 border border-gray-100 px-5 py-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2e7892]"
                    >
                    <p id="error_name" class="hidden mt-2 text-sm text-red-600 font-semibold"></p>
                </div>

                <div>
                    <label class="block mb-2 font-bold text-gray-800">Email</label>
                    <input
                        id="emailInput"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        class="w-full rounded-xl bg-gray-50 border border-gray-100 px-5 py-4 text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#2e7892]"
                    >
                    <p id="error_email" class="hidden mt-2 text-sm text-red-600 font-semibold"></p>
                </div>

                <div>
                    <label class="block mb-2 font-bold text-gray-800">Role</label>
                    <input
                        type="text"
                        value="{{ ucfirst($user->role ?? '-') }}"
                        readonly
                        class="w-full rounded-xl bg-gray-100 border border-gray-100 px-5 py-4 text-gray-500"
                    >
                </div>

                <div>
                    <label class="block mb-2 font-bold text-gray-800">Divisi</label>
                    <input
                        type="text"
                        value="{{ $user->division_name ?? '-' }}"
                        readonly
                        class="w-full rounded-xl bg-gray-100 border border-gray-100 px-5 py-4 text-gray-500"
                    >
                </div>

                <div>
                    <label class="block mb-2 font-bold text-gray-800">Password</label>
                    <input
                        type="password"
                        value="password"
                        readonly
                        class="w-full rounded-xl bg-gray-100 border border-gray-100 px-5 py-4 text-gray-500"
                    >
                    <p class="mt-2 text-sm text-gray-500">Password tidak diubah dari halaman ini.</p>
                </div>
            </div>

            <button
                id="submitButton"
                type="submit"
                class="mt-8 w-full rounded-xl bg-[#2e7892] px-6 py-4 text-white font-bold hover:opacity-90 disabled:opacity-60"
            >
                Simpan Perubahan
            </button>
        </form>

    </div>
</div>

<div id="cropModal" class="hidden fixed inset-0 z-50 bg-black/60 items-center justify-center px-5">
    <div class="bg-white rounded-3xl max-w-xl w-full p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-[#06496b]">Sesuaikan Foto</h2>
            <button type="button" id="closeCropModal" class="text-gray-500 font-bold text-2xl">&times;</button>
        </div>

        <div class="w-full max-h-[420px] overflow-hidden rounded-2xl bg-gray-100">
            <img id="cropImage" src="" class="max-w-full block">
        </div>

        <div class="grid grid-cols-2 gap-3 mt-5">
            <button type="button" id="cancelCrop" class="rounded-xl bg-gray-100 py-3 font-bold text-gray-700">
                Batal
            </button>
            <button type="button" id="applyCrop" class="rounded-xl bg-[#2e7892] py-3 font-bold text-white">
                Pakai Foto
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('profileForm');
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarEmpty = document.getElementById('avatarEmpty');
    const croppedAvatar = document.getElementById('croppedAvatar');
    const cropModal = document.getElementById('cropModal');
    const cropImage = document.getElementById('cropImage');
    const applyCrop = document.getElementById('applyCrop');
    const cancelCrop = document.getElementById('cancelCrop');
    const closeCropModal = document.getElementById('closeCropModal');
    const submitButton = document.getElementById('submitButton');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');

    let cropper = null;

    function openModal() {
        cropModal.classList.remove('hidden');
        cropModal.classList.add('flex');
    }

    function closeModal() {
        cropModal.classList.add('hidden');
        cropModal.classList.remove('flex');

        if (cropper) {
            cropper.destroy();
            cropper = null;
        }

        avatarInput.value = '';
    }

    function clearErrors() {
        ['name', 'email'].forEach(function (field) {
            const el = document.getElementById('error_' + field);

            if (el) {
                el.classList.add('hidden');
                el.textContent = '';
            }
        });

        errorMessage.classList.add('hidden');
        errorMessage.textContent = '';

        successMessage.classList.add('hidden');
        successMessage.textContent = '';
    }

    avatarInput.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) return;

        if (!file.type.startsWith('image/')) {
            errorMessage.textContent = 'File harus berupa gambar.';
            errorMessage.classList.remove('hidden');
            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {
            cropImage.src = e.target.result;
            openModal();

            setTimeout(function () {
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    background: false,
                    responsive: true,
                    cropBoxResizable: true,
                    cropBoxMovable: true,
                });
            }, 100);
        };

        reader.readAsDataURL(file);
    });

    applyCrop.addEventListener('click', function () {
        if (!cropper) return;

        const canvas = cropper.getCroppedCanvas({
            width: 500,
            height: 500,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);

        croppedAvatar.value = dataUrl;

        if (avatarEmpty) {
            avatarEmpty.classList.add('hidden');
        }

        avatarPreview.src = dataUrl;
        avatarPreview.classList.remove('hidden');

        closeModal();
    });

    cancelCrop.addEventListener('click', closeModal);
    closeCropModal.addEventListener('click', closeModal);

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        clearErrors();

        submitButton.disabled = true;
        submitButton.textContent = 'Menyimpan...';

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    Object.keys(data.errors).forEach(function (field) {
                        const el = document.getElementById('error_' + field);

                        if (el) {
                            el.textContent = data.errors[field][0];
                            el.classList.remove('hidden');
                        }
                    });
                } else {
                    errorMessage.textContent = data.message || 'Profil gagal diperbarui.';
                    errorMessage.classList.remove('hidden');
                }

                return;
            }

            successMessage.textContent = data.message || 'Profil berhasil diperbarui.';
            successMessage.classList.remove('hidden');

            if (data.user && data.user.avatar_url) {
                avatarPreview.src = data.user.avatar_url + '&t=' + Date.now();
                avatarPreview.classList.remove('hidden');
            }

            setTimeout(function () {
                window.location.href = "{{ route('profile.account') }}";
            }, 700);

        } catch (error) {
            errorMessage.textContent = 'Terjadi kesalahan. Coba lagi.';
            errorMessage.classList.remove('hidden');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Simpan Perubahan';
        }
    });
});
</script>
@endsection