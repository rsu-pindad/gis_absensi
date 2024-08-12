<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $npp = '';
    public string $email = '';
    public string $no_hp = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function rules()
    {
        return [
            'npp' => ['required', 'string','size:5','unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'no_hp' => ['required','string', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ];
    }

    public function mount()
    {
        if (old('npp')) {
	    $this->npp = old('npp');
        }
        if (old('email')) {
	    $this->npp = old('email');
        }
        if (old('no_hp')) {
	    $this->npp = old('no_hp');
        }
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'npp' => ['required', 'string','size:5','unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'no_hp' => ['required','string', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(RouteServiceProvider::HOME, navigate: true);
    }
}; ?>

<div class="mt-7 bg-neutral-50 border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-900 dark:border-neutral-700">
    <div class="p-4 sm:p-7">
        <div class="text-center">
            <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">Daftar</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-neutral-400">
                Sudah punya akun?
                <a class="text-blue-600 decoration-2 hover:underline focus:outline-none focus:underline font-medium dark:text-blue-500" href="{{ route('login') }}" wire:navigate="false">
                    Masuk
                </a>
            </p>
        </div>

        <div class="mt-5">
            <!-- Form -->
            <form wire:submit="register">
                <div class="grid gap-y-4">
                    <!-- Form Group -->
                    <div class="max-w-sm">
                        <div class="flex justify-between items-center">
                            <x-input-label for="npp" :value="__('NPP')" />
                            <x-input-corner :value="__('isi fixed:5')" />
                        </div>
                        <div class="relative">
                            <x-text-input wire:model="npp" id="npp" class="mt-1 py-3 px-4 ps-11" type="text" name="npp" placeholder="12345" required autocomplete="npp" />
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-4">
                                <x-ionicon-id-card-outline class="w-5 h-auto dark:text-neutral-400" />
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('npp')" class="px-4 mt-2" />
                    </div>
                    <!-- End Form Group -->

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

                    <!-- Form Group -->
                    <div class="max-w-sm">
                        <div class="flex justify-between items-center">
                            <x-input-label for="no_hp" :value="__('No Handphone')" />
                            <x-input-corner :value="__('isi minimal 9 digit')" />
                        </div>
                        <div class="relative">
                            <x-text-input wire:model="no_hp" id="no_hp" class="mt-1 py-3 px-4 ps-11" type="tel" name="no_hp" placeholder="0812345678" required autocomplete="no_hp" />
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-4">
                                <x-ionicon-phone-portrait-outline class="w-5 h-auto dark:text-neutral-400" />
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('no_hp')" class="px-4 mt-2" />
                    </div>
                    <!-- End Form Group -->

                    <!-- Form Group -->
                    <div class="max-w-sm">
                        <div class="flex justify-between items-center">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-input-corner :value="__('isi minimal 8 digit')" />
                        </div>
                        <div class="relative">
                            <x-text-input wire:model="password" id="password" class="mt-1 py-3 px-4 ps-11" type="password" name="password" placeholder="masukan password" required autocomplete="new-password" />
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-4">
                                <x-ionicon-key-outline class="w-5 h-auto dark:text-neutral-400" />
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <!-- End Form Group -->

                    <!-- Form Group -->
                    <div class="max-w-sm">
                        <div class="flex justify-between items-center">
                            <x-input-label for="password_confirmation" :value="__('Ulangi Password')" />
                            <x-input-corner :value="__('isi ulangi password')" />
                        </div>
                        <div class="relative">
                            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="mt-1 py-3 px-4 ps-11" type="password" name="password_confirmation" placeholder="ulangi password" required autocomplete="new-password" />
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-4">
                                <x-ionicon-key-outline class="w-5 h-auto dark:text-neutral-400" />
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                    <!-- End Form Group -->

                    <button type="submit" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                        Daftar
                    </button>
                </div>
            </form>
            <!-- End Form -->
        </div>
    </div>
</div>
