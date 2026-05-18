<x-login-layout>

    @if(session('login_user'))
        <script>
            let savedAccounts = JSON.parse(localStorage.getItem('saved_accounts') || '[]');
            let newAccount = @json(session('login_user'));

            savedAccounts = savedAccounts.filter(account => account.nip !== newAccount.nip);
            savedAccounts.unshift(newAccount);
            savedAccounts = savedAccounts.slice(0, 5);

            localStorage.setItem('saved_accounts', JSON.stringify(savedAccounts));
        </script>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <div id="savedAccounts" class="mb-6"></div>

        <div>
            <x-input-label for="nip" value="NIP" />
            <x-text-input
                id="nip"
                class="block mt-1 w-full"
                type="text"
                name="nip"
                :value="old('nip')"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('nip')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="remember">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember" style="margin-left:6px;">Remember me</label>
        </div>

        <button type="submit" class="login-btn">
            Login
        </button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const savedAccountsContainer = document.getElementById('savedAccounts');
            const nipInput = document.getElementById('nip');

            let accounts = JSON.parse(localStorage.getItem('saved_accounts') || '[]');

            if (!accounts.length) {
                return;
            }

            let html = `
                <div style="margin-bottom: 14px;">
                    <div style="font-weight: 700; color: #06496b; margin-bottom: 10px;">
                        Akun yang pernah login
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
            `;

            accounts.forEach(function (account, index) {
                const name = account.name || 'User';
                const nip = account.nip || '';
                const avatar = account.avatar_url || '';
                const initial = name.charAt(0).toUpperCase();

                html += `
                    <div
                        style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #e5e7eb; border-radius: 16px; cursor: pointer; background: #ffffff;"
                        onclick="selectSavedAccount('${nip}')"
                    >
                        ${
                            avatar
                                ? `<img src="${avatar}" style="width: 42px; height: 42px; border-radius: 9999px; object-fit: cover; border: 2px solid #2e7892;">`
                                : `<div style="width: 42px; height: 42px; border-radius: 9999px; background: #2e7892; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">${initial}</div>`
                        }

                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 700; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${name}
                            </div>
                            <div style="font-size: 13px; color: #6b7280;">
                                NIP: ${nip}
                            </div>
                        </div>

                        <button
                            type="button"
                            onclick="removeSavedAccount(event, ${index})"
                            style="border: none; background: #fee2e2; color: #dc2626; border-radius: 9999px; width: 28px; height: 28px; font-weight: 700; cursor: pointer;"
                            title="Hapus akun dari device ini"
                        >
                            ×
                        </button>
                    </div>
                `;
            });

            html += `
                    </div>
                </div>
            `;

            savedAccountsContainer.innerHTML = html;
        });

        function selectSavedAccount(nip) {
            const nipInput = document.getElementById('nip');
            const passwordInput = document.getElementById('password');

            nipInput.value = nip;
            passwordInput.focus();
        }

        function removeSavedAccount(event, index) {
            event.stopPropagation();

            let accounts = JSON.parse(localStorage.getItem('saved_accounts') || '[]');

            accounts.splice(index, 1);

            localStorage.setItem('saved_accounts', JSON.stringify(accounts));

            window.location.reload();
        }
    </script>

</x-login-layout>