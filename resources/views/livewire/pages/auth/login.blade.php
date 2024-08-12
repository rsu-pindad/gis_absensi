<?php

use App\Livewire\Forms\LoginForm;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function mount()
    {
        if(old('form.npp')){
            $form->npp = old('npp');
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        $this->reset('form.password');

        Session::regenerate();

        $this->redirectIntended(default: RouteServiceProvider::HOME, navigate: true);
    }
}; ?>

<div class="m-10 bg-neutral-50 border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-900 dark:border-neutral-700">
    <div class="p-4 sm:p-7 flex flex-col items-center justify-center">
        <div class="text-center">
            <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Masuk</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-neutral-400">
                Belum punya akun?
                <a class="text-blue-600 decoration-2 hover:underline focus:outline-none focus:underline font-medium dark:text-blue-500" href="{{ route('register') }}" wire:navigate="false">
                    Daftar
                </a>
            </p>
        </div>

        <div class="text-center mt-3">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form wire:submit="login">
                <div class="grid gap-y-4">
                    <!-- Npp -->
                    <div class="max-w-sm">
                        <div class="flex justify-between items-center">
                            <x-input-label for="npp" :value="__('Npp')" />
                        </div>
                        <div class="relative">
                            <x-text-input wire:model="form.npp" id="npp" class="mt-1 py-3 px-4 ps-11" type="text" name="npp" required autocomplete="npp" placeholder="masukan npp" />
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-4">
                                <x-ionicon-id-card-outline class="w-5 h-auto dark:text-neutral-400" />
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('form.npp')" class="px-4 mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="max-w-sm">
                        <div class="flex justify-between items-center">
                            <x-input-label for="password" :value="__('Password')" />
                        </div>
                        <div class="relative">
                            <x-text-input wire:model="form.password" id="password" class="mt-1 py-3 px-4 ps-11" type="password" name="password" required autocomplete="current-password" placeholder="masukan password" />
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-4">
                                <x-ionicon-key-outline class="w-5 h-auto dark:text-neutral-400" />
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('form.password')" class="px-4 mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="block mt-3">
                        <label for="remember" class="inline-flex items-center">
                            <input wire:model="form.remember" id="remember" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Ingat Saya') }}</span>
                        </label>
                    </div>

                    <div class="max-w-sm">
                        <button type="submit" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">Masuk</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="text-center mt-3">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}" wire:navigate="false">
                    {{ __('Lupa password ?') }}
                </a>
            @endif
        </div>
    </div>
</div>
