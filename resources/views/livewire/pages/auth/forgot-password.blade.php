<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="mt-7 bg-neutral-50 border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-900 dark:border-neutral-700">
    <div class="p-4 sm:p-7">
        <div class="text-center">
            <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Lupa Password?</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-neutral-400">
                jangan khawatir, kami hanya memerlukan email anda, tautan pemulihan password akan dikirim melalui email
            </p>
        </div>

        <div class="mt-5">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Form -->
            <form wire:submit="sendPasswordResetLink">
                <div class="grid gap-y-4">
                    <!-- Form Group -->
                    <div class="max-w-sm">
                        <div class="flex justify-between items-center">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-input-corner :value="__('isi')" />
                        </div>
                        <div class="relative">
                            <x-text-input wire:model="email" id="email" class="mt-1 py-3 px-4 ps-11" type="email" name="email" placeholder="pegawai@pindadmedika.com" required autocomplete="email" />
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-4">
                                <x-ionicon-mail-outline class="w-5 h-auto dark:text-neutral-400" />
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="px-4 mt-2" />
                    </div>
                    <!-- End Form Group -->

                    <button type="submit" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                        Reset password
                    </button>
                </div>
            </form>
            <!-- End Form -->
        </div>
    </div>
</div>