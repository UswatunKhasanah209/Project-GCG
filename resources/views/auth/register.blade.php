<x-guest-layout>
    @push('styles')
        <style>
            body {
                background-image: url('{{ asset('images/BG register.png') }}') !important;
            }
        </style>
    @endpush

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name') }}"
                required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Division -->
        <div class="mt-4">
            <x-input-label for="division_id" value="Divisi" />

            <select id="division_id" name="division_id" required
                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">-- PILIH DIVISI --</option>

                @foreach ($divisions as $division)
                    <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                        {{ $division->name }}
                    </option>
                @endforeach
            </select>

            <x-input-error :messages="$errors->get('division_id')" class="mt-2" />
        </div>

        <!-- Department -->
        <div class="mt-4">
            <x-input-label for="department" value="Departemen (Opsional)" />
            <x-text-input id="department" class="block mt-1 w-full" type="text" name="department"
                value="{{ old('department') }}" />
            <x-input-error :messages="$errors->get('department')" class="mt-2" />
        </div>

        <!-- Bagian -->
        <div class="mt-4">
            <x-input-label for="bagian" :value="__('Bagian')" />
            <select id="bagian" name="bagian" class="block mt-1 w-full border-gray-300 rounded-md">
                <option value="">-- PILIH BAGIAN --</option>
                <option value="tata kelola">Tata Kelola</option>
                <option value="other">Other</option>
            </select>
            <x-input-error :messages="$errors->get('bagian')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                value="{{ old('email') }}" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600" href="{{ route('login') }}">
                Sudah punya akun?
            </a>

            <x-primary-button class="ms-4">
                Register
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
